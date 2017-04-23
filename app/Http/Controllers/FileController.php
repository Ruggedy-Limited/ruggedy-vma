<?php

namespace App\Http\Controllers;
use App\Commands\DeleteFile;
use App\Commands\EditFile;
use App\Commands\GetFile;
use App\Commands\GetWorkspaceApp;
use App\Commands\UploadScanOutput;
use App\Entities\File;

/**
 * @Middleware("web")
 */
class FileController extends AbstractController
{
    /**
     * Show a file and related data including Assets, Vulnerabilities and Comments
     *
     * @GET("/workspace/app/file/{fileId}", as="file.view", where={"fileId":"[0-9]+"})
     *
     * @param $fileId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function viewFile($fileId)
    {
        $command  = new GetFile(intval($fileId));
        $fileInfo = $this->sendCommandToBusHelper($command);

        if ($this->isCommandError($fileInfo)) {
            return redirect()->back();
        }

        return view('workspaces.app-show', $fileInfo);
    }

    /**
     * Display the form where scan output files can be uploaded
     *
     * @GET("/workspace/app/file/create/{workspaceAppId}", as="file.create",
     *     where={"workspaceAppId":"[0-9]+"})
     *
     * @param $workspaceAppId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function addFile($workspaceAppId)
    {
        $command      = new GetWorkspaceApp(intval($workspaceAppId));
        $workspaceApp = $this->sendCommandToBusHelper($command);

        if ($this->isCommandError($workspaceApp)) {
            return redirect()->back();
        }

        return view('workspaces.addFile', ['workspaceApp' => $workspaceApp]);
    }

    /**
     * Upload scanner output for processing
     *
     * @POST("/workspace/app/file/upload/{workspaceAppId}", as="file.upload",
     *     where={"workspaceAppId":"[0-9]+"})
     *
     * @param $workspaceAppId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function uploadFile($workspaceAppId)
    {
        // Validate the request
        $this->validate($this->request, $this->getValidationRules(), $this->getValidationMessages());

        // Initialise a new File entity to send with the command
        $file = new File();
        $file->setName($this->request->get('name'))
             ->setDescription($this->request->get('description'));

        // Create and send the command to upload the scanner output file
        $command  = new UploadScanOutput(intval($workspaceAppId), $this->request->file('file'), $file);
        $file     = $this->sendCommandToBusHelper($command);

        if ($this->isCommandError($file)) {
            return redirect()->back()->withInput();
        }

        return redirect()->route('app.view', ['workspaceApp' => $file->getWorkspaceApp()->getId()]);
    }

    /**
     * Display the form for editing a file's name and description
     *
     * @GET("/workspace/app/file/edit/{fileId}", as="file.edit", where={"fileId":"[0-9]+"})
     *
     * @param $fileId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function editFile($fileId)
    {
        $command  = new GetFile(intval($fileId));
        $fileInfo = $this->sendCommandToBusHelper($command);

        if ($this->isCommandError($fileInfo)) {
            return redirect()->back();
        }

        return view('workspaces.edit-file', ['file' => $fileInfo['file']]);
    }

    /**
     * Display the form for editing a file's name and description
     *
     * @POST("/workspace/app/file/update/{fileId}", as="file.update", where={"fileId":"[0-9]+"})
     *
     * @param $fileId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function updateFile($fileId)
    {
        // Validate the request
        $this->validate($this->request, $this->getValidationRules(), $this->getValidationMessages());

        $command  = new EditFile($fileId, $this->request->all());
        $file     = $this->sendCommandToBusHelper($command);

        if ($this->isCommandError($file)) {
            return redirect()->back()->withInput();
        }

        return redirect()->route('file.view', ['fileId' => $fileId]);
    }

    /**
     * Display the form for editing a file's name and description
     *
     * @GET("/workspace/app/file/delete/{fileId}/{workspaceAppId}", as="file.delete",
     *     where={"fileId":"[0-9]+","workspaceAppId":"[0-9]+"})
     *
     * @param $fileId
     * @param $workspaceAppId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function deleteFile($fileId, $workspaceAppId)
    {
        $command = new DeleteFile(intval($fileId), true);
        $file    = $this->sendCommandToBusHelper($command);

        if ($this->isCommandError($file)) {
            return redirect()->back();
        }

        $this->flashMessenger->success('File deleted successfully.');
        return redirect()->route('app.view', ['workspaceAppId' => $workspaceAppId]);
    }

    /**
     * @inheritdoc
     *
     * @return array
     */
    protected function getValidationRules(): array
    {
        return [
            'name' => 'bail|filled',
            'file' => 'bail|filled|file',
        ];
    }

    /**
     * @inheritdoc
     *
     * @return array
     */
    protected function getValidationMessages(): array
    {
        return [
            'name.filled' => 'A name is required to create a new File but it does not seem like you entered one. '
                .'Please try again.',
            'file'        => [
                'filled'   => 'A file must be selected to create a new file entry and upload the file but it does not '
                    . 'seem like you selected one. Please try again.',
                'file'     => 'Either the selected file was not valid, or the file upload failed. Please try again.',
            ],
        ];
    }
}