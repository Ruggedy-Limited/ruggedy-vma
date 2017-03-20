<?php

namespace App\Http\Controllers;

use App\Commands\CreateFolder;
use App\Commands\DeleteFolder;
use App\Commands\EditFolder;
use App\Commands\GetFolder;
use App\Entities\Folder;
use Illuminate\Http\Request;

/**
 * @Middleware("web")
 */
class FolderController extends AbstractController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('folders.index');
    }

    /**
     * Show the form for creating a folder.
     *
     * @GET("/workspace/folder/create/{workspaceId}", as="workspace.folder.create", where={"workspaceId":"[0-9]+"})
     *
     * @param $workspaceId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function create($workspaceId)
    {
        return $this->controllerResponseHelper(null, 'folders.create', ['workspaceId' => intval($workspaceId)]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @POST("/workspace/folder/store/{workspaceId}", as="workspace.folder.store", where={"workspaceId":"[0-9]+"})
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function store(Request $request, $workspaceId)
    {
        $this->validate($request, $this->getValidationRules(),$this->getValidationMessages());

        $folder = new Folder();
        $folder->setName($request->get('name'))
            ->setDescription($request->get('description'));

        $command  = new CreateFolder(intval($workspaceId), $folder);
        $response = $this->sendCommandToBusHelper($command);

        $this->addMessage('Folder created successfully.', parent::MESSAGE_TYPE_SUCCESS);
        return $this->controllerResponseHelper($response, 'workspace.view', ['workspaceId' => $workspaceId], true);
    }

    /**
     * Display the specified resource.
     *
     * @GET("/workspace/folder/{folderId}", as="workspace.folder.view", where={"folderId":"[0-9]+"})
     *
     * @param $folderId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function show($folderId)
    {
        $command  = new GetFolder(intval($folderId));
        $response = $this->sendCommandToBusHelper($command);

        return $this->controllerResponseHelper($response, 'folders.show', ['folder' => $response]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @GET("/workspace/folder/{folderId}", as="workspace.folder.view", where={"folderId":"[0-9]+"})
     *
     * @param $folderId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit($folderId)
    {
        $command  = new GetFolder(intval($folderId));
        $response = $this->sendCommandToBusHelper($command);

        return $this->controllerResponseHelper($response, 'folders.create', ['workspace' => $response]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @POST("/workspace/folder/update/{folderId}", as="workspace.folder.view", where={"folderId":"[0-9]+"})
     *
     * @param  int  $folderId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function update($folderId)
    {
        $this->validate($this->request, $this->getValidationRules(), $this->getValidationMessages());

        $command  = new EditFolder(intval($folderId), $this->request->all());
        $response = $this->sendCommandToBusHelper($command);

        return $this->controllerResponseHelper($response, 'workspace.folder.view', ['folderId' => $folderId], true);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $folderId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function destroy($folderId)
    {
        $command  = new DeleteFolder($folderId, true);
        $response = $this->sendCommandToBusHelper($command);

        return $this->controllerResponseHelper(
            $response,
            'workspace.view',
            ['workspaceId' => $response->getWorkspaceId()],
            true
        );
    }

    /**
     * @inheritdoc
     *
     * @return array
     */
    protected function getValidationRules(): array
    {
        return [
            Folder::NAME        => 'bail|filled',
            Folder::DESCRIPTION => 'bail|filled',
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
            Folder::NAME        => 'You must enter a name for the folder.',
            Folder::DESCRIPTION => 'You must enter a description for the folder.',
        ];
    }
}
