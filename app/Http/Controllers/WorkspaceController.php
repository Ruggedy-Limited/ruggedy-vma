<?php

namespace App\Http\Controllers;

use App\Commands\CreateWorkspace;
use App\Commands\CreateWorkspaceApp;
use App\Commands\DeleteWorkspace;
use App\Commands\DeleteWorkspaceApp;
use App\Commands\EditWorkspace;
use App\Commands\GetFile;
use App\Commands\GetListOfScannerApps;
use App\Commands\GetScannerApp;
use App\Commands\GetVulnerability;
use App\Commands\GetWorkspace;
use App\Commands\GetWorkspaceApp;
use App\Commands\UploadScanOutput;
use App\Entities\File;
use App\Entities\Workspace;
use App\Entities\WorkspaceApp;
use Auth;

/**
 * @Middleware("web")
 */
class WorkspaceController extends AbstractController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('workspaces.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @GET("/workspace/create", as="workspace.create")
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('workspaces.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @POST("/workspace/store", as="workspace.store")
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function store()
    {
        // Validate the request
        $this->validate($this->request, $this->getValidationRules(), $this->getValidationMessages());

        // Get the authenticated User ID
        $userId = Auth::user() ? Auth::user()->getId() : 0;

        // Create a new Workspace entity and populate the name & description from the request
        $entity = new Workspace();
        $entity->setName($this->request->get('name'));
        $entity->setDescription($this->request->get('description'));

        // Create a command and send it over the bus to the handler
        $command = new CreateWorkspace($userId, $entity);
        $response = $this->sendCommandToBusHelper($command);

        $this->addMessage("Workspace created successfully.", parent::MESSAGE_TYPE_SUCCESS);
        return $this->controllerResponseHelper($response, 'home', [], true);
    }

    /**
     * Display the specified resource.
     *
     * @GET("/workspace/{workspaceId}", as="workspace.view", where={"workspaceId":"[0-9]+"})
     *
     * @param  int  $workspaceId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function view($workspaceId)
    {
        // Create a command and send it over the bus to the handler
        $command = new GetWorkspace(intval($workspaceId));
        $response = $this->sendCommandToBusHelper($command);

        return $this->controllerResponseHelper($response, 'workspaces.view', ['workspace' => $response]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @GET("/workspace/edit/{workspaceId}", as="workspace.edit", where={"workspaceId":"[0-9]+"})
     *
     * @param  int  $workspaceId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit($workspaceId)
    {
        $command = new GetWorkspace(intval($workspaceId));
        $response = $this->sendCommandToBusHelper($command);

        return $this->controllerResponseHelper($response, 'workspace.edit', ['workspace' => $response]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @POST("/workspace/update/{workspaceId}", as="workspace.update", where={"workspaceId":"[0-9]+"})
     *
     * @param $workspaceId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function update($workspaceId)
    {
        // Validate the request
        $this->validate($this->request, $this->getValidationRules(), $this->getValidationMessages());

        // Create a command and send it over the bus to the handler
        $command = new EditWorkspace($workspaceId, $this->request->all());
        $response = $this->sendCommandToBusHelper($command);

        $this->addMessage("Workspace updated successfully.", parent::MESSAGE_TYPE_SUCCESS);
        return $this->controllerResponseHelper($response, 'home', [], true);
    }

    /**
     * Remove the Workspace and all related data.
     *
     * @GET("/workspace/delete/{workspaceId}", as="workspace.delete", where={"workspaceId":"[0-9]+"})
     *
     * @param  int  $workspaceId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function destroy($workspaceId)
    {
        // Create a new command and send it over the bus
        $command  = new DeleteWorkspace($workspaceId, true);
        $response = $this->sendCommandToBusHelper($command);

        // Add a success message and then generate a response for the Controller
        $this->addMessage("Workspace deleted successfully.", parent::MESSAGE_TYPE_SUCCESS);
        return $this->controllerResponseHelper($response, 'home', [], true);
    }

    /**
     * Get a full list of all possible ScannerApps
     *
     * @GET("/workspace/apps/{workspaceId}", as="workspace.apps", where={"workspaceId":"[0-9]+"})
     *
     * @param $workspaceId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function apps($workspaceId)
    {
        $command     = new GetListOfScannerApps(0);
        $scannerApps = $this->sendCommandToBusHelper($command);

	    return $this->controllerResponseHelper(null, 'workspaces.apps', [
            'scannerApps' => $scannerApps,
            'workspaceId' => $workspaceId,
        ]);
    }

    /**
     * Display the form to create a WorkspaceApp
     *
     * @GET("/workspace/app/create/{workspaceId}/{scannerAppId}", as="workspace.app.create",
     *     where={"workspaceId":"[0-9]+","scannerAppId":"[0-9]+"})
     *
     * @param $workspaceId
     * @param $scannerAppId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function createWorkspaceApp($workspaceId, $scannerAppId)
    {
        $command    = new GetScannerApp($scannerAppId);
        $scannerApp = $this->sendCommandToBusHelper($command);

        return $this->controllerResponseHelper($scannerApp, 'workspaces.appsCreate', [
            'workspaceId'  => $workspaceId,
            'scannerAppId' => $scannerAppId,
            'scannerApp'   => $scannerApp,
        ]);
    }

    /**
     * Save a new WorkspaceApp
     *
     * @POST("/workspace/app/store/{workspaceId}/{scannerAppId}", as="workspace.app.store",
     *     where={"workspaceId":"[0-9]+","scannerAppId":"[0-9]+"})
     *
     * @param $workspaceId
     * @param $scannerAppId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function storeWorkspaceApp($workspaceId, $scannerAppId)
    {
        $this->validate($this->request, $this->getValidationRules(), $this->getValidationMessages());

        $workspaceApp = new WorkspaceApp();
        $workspaceApp->setName($this->request->get('name'))
            ->setDescription($this->request->get('description'));

        $command = new CreateWorkspaceApp($workspaceId, $scannerAppId, $workspaceApp);
        $response = $this->sendCommandToBusHelper($command);

        $this->addMessage("New Workspace App created successfully.", parent::MESSAGE_TYPE_SUCCESS);
        return $this->controllerResponseHelper($response, 'workspace.view', ['workspaceId' => $workspaceId], true);
    }

    /**
     * Get a WorkspaceApp and related data
     *
     * @GET("/workspace/app/{workspaceAppId}", as="workspace.app.view", where={"workspaceAppId":"[0-9]+"})
     *
     * @param $workspaceAppId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function viewWorkspaceApp($workspaceAppId)
    {
        $command  = new GetWorkspaceApp($workspaceAppId);
        $response = $this->sendCommandToBusHelper($command);

        return $this->controllerResponseHelper($response, 'workspaces.app', ['workspaceApp' => $response]);
    }

    /**
     * Delete WorkspaceApp and all related data
     *
     * @GET("/workspace/app/delete/{workspaceId}/{workspaceAppId}", as="workspace.app.delete",
     *     where={"workspaceId":"[0-9]+","workspaceAppId":"[0-9]+"})
     *
     * @param $workspaceAppId
     * @param $workspaceId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function deleteWorkspaceApp($workspaceAppId, $workspaceId)
    {
        $command  = new DeleteWorkspaceApp($workspaceAppId, true);
        $response = $this->sendCommandToBusHelper($command);

        $this->addMessage("Workspace App deleted successfully.", parent::MESSAGE_TYPE_SUCCESS);
        return $this->controllerResponseHelper($response, 'workspace.view', ['workspaceId' => $workspaceId], true);
    }

    /**
     * Get a single Vulnerability record related to a specific file
     *
     * @GET("/file/vulnerability/{fileId}/{vulnerabilityID}", as="file.vulnerability.view",
     *     where={"fileId":"[0-9]+","vulnerabilityId":"[0-9]+"})
     *
     * @param $fileId
     * @param $vulnerabilityId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function appShowRecord($fileId, $vulnerabilityId)
    {
        $command  = new GetVulnerability($vulnerabilityId, $fileId);
        $response = $this->sendCommandToBusHelper($command);
        return $this->controllerResponseHelper(
            $response,
            'workspaces.appShowRecord',
            ['vulnerability' => $response, 'assets' => $response->getAssets()]
        );
    }

    /**
     * Show a file and related data including Assets, Vulnerabilities and Comments
     *
     * @GET("/workspace/app/file/{fileId}", as="workspace.app.file.view", where={"fileId":"[0-9]+"})
     *
     * @param $fileId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function viewFile($fileId)
    {
        $command  = new GetFile($fileId);
        $response = $this->sendCommandToBusHelper($command);
        return $this->controllerResponseHelper(
            $response,
            'workspaces.app-show',
            ['file' => $response, 'assets' => $response->getAssets()]
        );
    }

    /**
     * Display the form where scan output files can be uploaded
     *
     * @GET("/workspace/app/file/create/{workspaceAppId}", as="workspace.app.file.form", where={"workspaceAppId":"[0-9]+"})
     *
     * @param $workspaceAppId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function addFile($workspaceAppId)
    {
        $command      = new GetWorkspaceApp($workspaceAppId);
        $workspaceApp = $this->sendCommandToBusHelper($command);
        return $this->controllerResponseHelper($workspaceApp, 'workspaces.addFile', ['workspaceApp' => $workspaceApp]);
    }

    /**
     * Upload scanner output for processing
     *
     * @POST("/workspace/app/file/upload/{workspaceAppId}", as="workspace.app.file.upload",
     *     where={"workspaceAppId":"[0-9]+"})
     *
     * @param $workspaceAppId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function uploadFile($workspaceAppId)
    {
        // Initialise a new File entity to send with the command
        $file = new File();
        $file->setName($this->request->get('name'))
            ->setDescription($this->request->get('description'));

        // Create and send the command to upload the scanner output file
        $command  = new UploadScanOutput($workspaceAppId, $this->request->file('file'), $file);
        $response = $this->sendCommandToBusHelper($command);

        return $this->controllerResponseHelper(
            $response,
            'workspace.app.view',
            ['workspaceApp' => $file->getWorkspaceApp()->getId()],
            true
        );
    }

    public function ruggedyIndex() {
        return view ('workspaces.ruggedyIndex');
    }

    public function ruggedyCreate() {
        return view ('workspaces.ruggedyCreate');
    }

    public function ruggedyShow() {
        return view ('workspaces.ruggedyShow');
    }

    /**
     * @inheritdoc
     *
     * @return array
     */
    protected function getValidationRules(): array
    {
        return [
            Workspace::NAME        => 'bail|filled',
            Workspace::DESCRIPTION => 'bail|filled',
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
            Workspace::NAME        => 'You must give the Workspace a name',
            Workspace::DESCRIPTION => 'You must give the Workspace a description',
        ];
    }
}

