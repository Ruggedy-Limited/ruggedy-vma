<?php

namespace App\Http\Controllers;
use App\Commands\DeleteFile;
use App\Commands\EditFile;
use App\Commands\GetFile;
use App\Commands\GetWorkspaceApp;
use App\Commands\UploadScanOutput;
use App\Entities\File;
use App\Policies\ComponentPolicy;
use Auth;

/**
 * @Middleware({"web", "auth"})
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

        return view('workspaces.app-show', [
            'file'            => $fileInfo->get('file'),
            'vulnerabilities' => $fileInfo->get('vulnerabilities'),
            'assets'          => $fileInfo->get('assets'),
        ]);
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
        $command          = new GetWorkspaceApp(intval($workspaceAppId));
        $workspaceAppInfo = $this->sendCommandToBusHelper($command);

        if ($this->isCommandError($workspaceAppInfo)) {
            return redirect()->back();
        }

        if (Auth::user()->cannot(ComponentPolicy::ACTION_CREATE, $workspaceAppInfo->get('app'))) {
            $this->flashMessenger->error("You do not have permission to add Files to this App.");
            return redirect()->back();
        }

        return view('workspaces.addFile', ['workspaceApp' => $workspaceAppInfo->get('app')]);
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
        $rules = $this->getValidationRules();
        $rules['file'] = 'bail|required|file';

        $messages = $this->getValidationMessages();
        $messages['file'] = [
            'required' => 'A file must be selected to create a new file entry and upload the file but it does not '
                . 'seem like you selected one. Please try again.',
            'file'     => 'Either the selected file was not valid, or the file upload failed. Please try again.',
        ];

        // Validate the request
        $this->validate($this->request, $rules, $messages);

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

        if (Auth::user()->cannot(ComponentPolicy::ACTION_EDIT, $fileInfo->get('file'))) {
            $this->flashMessenger->error("You do not have permission to edit that File.");
            return redirect()->back();
        }

        return view('workspaces.edit-file', ['file' => $fileInfo->get('file')]);
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
                . 'Please try again.',
        ];
    }
}