<?php

namespace App\Http\Controllers;

use App\Commands\AddRemoveVulnerabilityToFromFolder;
use App\Commands\CreateFolder;
use App\Commands\DeleteFolder;
use App\Commands\EditFolder;
use App\Commands\GetFolder;
use App\Commands\GetWorkspace;
use App\Entities\Folder;
use Illuminate\Http\Request;

/**
 * @Middleware("web")
 */
class FolderController extends AbstractController
{
    /**
     * Show the form for creating a folder.
     *
     * @GET("/workspace/folder/create/{workspaceId}", as="folder.create", where={"workspaceId":"[0-9]+"})
     *
     * @param $workspaceId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function create($workspaceId)
    {
        $command   = new GetWorkspace(intval($workspaceId));
        $workspace = $this->sendCommandToBusHelper($command);

        if ($this->isCommandError($workspace)) {
            return redirect()->back();
        }

        return view('folders.create', [
            'workspaceId' => intval($workspaceId),
            'workspace'   => $workspace,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @POST("/workspace/folder/store/{workspaceId}", as="folder.store", where={"workspaceId":"[0-9]+"})
     *
     * @param  \Illuminate\Http\Request $request
     * @param $workspaceId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function store(Request $request, $workspaceId)
    {
        $this->validate($request, $this->getValidationRules(),$this->getValidationMessages());

        $folder = new Folder();
        $folder->setName($request->get('name'))
            ->setDescription($request->get('description'));

        $command = new CreateFolder(intval($workspaceId), $folder);
        $folder  = $this->sendCommandToBusHelper($command);

        if ($this->isCommandError($folder)) {
            return redirect()->back()->withInput();
        }

        $this->flashMessenger->success('Folder created successfully.');
        return redirect()->route('workspace.view', ['workspaceId' => $workspaceId]);
    }

    /**
     * Display the specified resource.
     *
     * @GET("/workspace/folder/{folderId}", as="folder.view", where={"folderId":"[0-9]+"})
     *
     * @param $folderId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function show($folderId)
    {
        $command = new GetFolder(intval($folderId));
        $result  = $this->sendCommandToBusHelper($command);

        if ($this->isCommandError($result)) {
            return redirect()->back();
        }

        return view('folders.index', $result);
    }

    /**
     * Show the form for editing a Folder.
     *
     * @GET("/workspace/folder/edit/{folderId}", as="folder.edit", where={"folderId":"[0-9]+"})
     *
     * @param $folderId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit($folderId)
    {
        $command     = new GetFolder(intval($folderId));
        $folderInfo  = $this->sendCommandToBusHelper($command);

        if ($this->isCommandError($folderInfo)) {
            return redirect()->back()->withInput();
        }

        return view('folders.edit', ['folder' => $folderInfo['folder']]);
    }

    /**
     * Update the Folder details in storage.
     *
     * @POST("/workspace/folder/update/{folderId}", as="folder.update", where={"folderId":"[0-9]+"})
     *
     * @param  int  $folderId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function update($folderId)
    {
        $this->validate($this->request, $this->getValidationRules(), $this->getValidationMessages());

        $command = new EditFolder(intval($folderId), $this->request->all());
        $folder  = $this->sendCommandToBusHelper($command);

        if ($this->isCommandError($folder)) {
            redirect()->back()->withInput();
        }

        return redirect()->route('folder.view', ['folderId' => $folderId]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @GET("/workspace/folder/delete/{folderId}", as="folder.delete", where={"folderId":"[0-9]+"})
     *
     * @param  int  $folderId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function destroy($folderId)
    {
        $command = new DeleteFolder($folderId, true);
        $folder  = $this->sendCommandToBusHelper($command);

        if ($this->isCommandError($folder)) {
            redirect()->back();
        }

        $this->flashMessenger->success('Folder deleted successfully.');
        return redirect()->route('workspace.view', ['workspaceId' => $folder->getWorkspaceId()]);
    }

    /**
     * Add a Vulnerability to a Folder
     *
     * @POST("/vulnerability/folder/add/{vulnerabilityId}", as="vulnerability.folder.add",
     *     where={"vulnerabilityId":"[0-9]+","folderId":"[0-9]+"})
     *
     * @param $vulnerabilityId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function addVulnerabilityToFolder($vulnerabilityId)
    {
        // Validate the request
        $this->validate($this->request, ['folder-id' => 'bail|required|int'], [
            'folder-id' => [
                'required' => 'Please select the Folder where you want the Vulnerability added.',
                'int'      => "That doesn't seem to be a valid Folder selection. Please try again.",
            ]
        ]);

        $folderId = $this->request->get('folder-id', 0);
        $command  = new AddRemoveVulnerabilityToFromFolder(intval($folderId), intval($vulnerabilityId));
        $folder   = $this->sendCommandToBusHelper($command);

        if ($this->isCommandError($folder)) {
            redirect()->back();
        }

        $this->flashMessenger->success("Vulnerability successfully added to folder.");
        return redirect()->route('vulnerability.view', ['vulnerabilityId' => $vulnerabilityId]);
    }

    /**
     * Remove a Vulnerability from a Folder
     *
     * @GET("/vulnerability/folder/remove/{vulnerabilityId}/{folderId}", as="vulnerability.folder.remove",
     *     where={"vulnerabilityId":"[0-9]+","folderId":"[0-9]+"})
     *
     * @param $vulnerabilityId
     * @param $folderId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function removeVulnerabilityToFolder($vulnerabilityId, $folderId)
    {
        $command  = new AddRemoveVulnerabilityToFromFolder(intval($folderId), intval($vulnerabilityId), true);
        $folder   = $this->sendCommandToBusHelper($command);

        if ($this->isCommandError($folder)) {
            redirect()->back();
        }

        $this->flashMessenger->success("Vulnerability successfully removed from folder.");

        return redirect()->route('folder.view', ['folderId' => $folderId]);

    }

    /**
     * @inheritdoc
     *
     * @return array
     */
    protected function getValidationRules(): array
    {
        return [
            'name' => 'bail|required',
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
            'name.required' => 'A name is required to create a folder but it does not seem like you entered one. '
                . 'Please try again.',
        ];
    }
}
