<?php

namespace App\Http\Controllers;

use App\Commands\CreateWorkspaceApp;
use App\Commands\DeleteWorkspaceApp;
use App\Commands\EditWorkspaceApp;
use App\Commands\GetFile;
use App\Commands\GetListOfScannerApps;
use App\Commands\GetScannerApp;
use App\Commands\GetWorkspace;
use App\Commands\GetWorkspaceApp;
use App\Entities\WorkspaceApp;

/**
 * @Middleware("web")
 */
class AppController extends AbstractController
{
    /**
     * Get a WorkspaceApp and related data
     *
     * @GET("/workspace/app/{workspaceAppId}", as="app.view", where={"workspaceAppId":"[0-9]+"})
     *
     * @param $workspaceAppId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function viewWorkspaceApp($workspaceAppId)
    {
        $command      = new GetWorkspaceApp(intval($workspaceAppId));
        /** @var WorkspaceApp $workspaceApp */
        $workspaceApp = $this->sendCommandToBusHelper($command);

        if ($this->isCommandError($workspaceApp)) {
            return redirect()->back();
        }

        return view('workspaces.app', ['workspaceApp' => $workspaceApp]);
    }

    /**
     * Show a file and related data including Assets, Vulnerabilities and Comments
     *
     * @GET("/workspace/ruggedy-app/{fileId}", as="ruggedy-app.view", where={"fileId":"[0-9]+"})
     *
     * @param $fileId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function viewRuggedyApp($fileId)
    {
        $command  = new GetFile(intval($fileId));
        $fileInfo = $this->sendCommandToBusHelper($command);

        if ($this->isCommandError($fileInfo)) {
            return redirect()->back();
        }

        return view('workspaces.ruggedy-app-show', $fileInfo);
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

        $command   = new GetWorkspace(intval($workspaceId));
        $workspace = $this->sendCommandToBusHelper($command);

        if ($this->isCommandError($scannerApps) || $this->isCommandError($workspace)) {
            return redirect()->back();
        }

        return view('workspaces.apps', [
            'scannerApps' => $scannerApps,
            'workspace'   => $workspace,
            'workspaceId' => $workspaceId,
        ]);
    }

    /**
     * Display the form to create a WorkspaceApp
     *
     * @GET("/workspace/app/create/{workspaceId}/{scannerAppId}", as="app.create",
     *     where={"workspaceId":"[0-9]+","scannerAppId":"[0-9]+"})
     *
     * @param $workspaceId
     * @param $scannerAppId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function createWorkspaceApp($workspaceId, $scannerAppId)
    {
        $command    = new GetScannerApp(intval($scannerAppId));
        $scannerApp = $this->sendCommandToBusHelper($command);

        $command   = new GetWorkspace(intval($workspaceId));
        $workspace = $this->sendCommandToBusHelper($command);

        if ($this->isCommandError($scannerApp) || $this->isCommandError($workspace)) {
            return redirect()->back();
        }

        return view('workspaces.appsCreate', [
            'workspaceId'  => $workspaceId,
            'workspace'    => $workspace,
            'scannerAppId' => $scannerAppId,
            'scannerApp'   => $scannerApp,
        ]);
    }

    /**
     * Display the form to edit a Workspace App
     *
     * @GET("/workspace/app/edit/{workspaceAppId}", as="app.edit", where={"workspaceAppId":"[0-9]+"})
     *
     * @param $workspaceAppId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function editWorkspaceApp($workspaceAppId)
    {
        $command      = new GetWorkspaceApp(intval($workspaceAppId));
        $workspaceApp = $this->sendCommandToBusHelper($command);

        if ($this->isCommandError($workspaceApp)) {
            return redirect()->back();
        }

        return view('workspaces.app-edit', ['workspaceApp' => $workspaceApp]);
    }

    /**
     * Save a new WorkspaceApp
     *
     * @POST("/workspace/app/store/{workspaceId}/{scannerAppId}", as="app.store",
     *     where={"workspaceId":"[0-9]+","scannerAppId":"[0-9]+"})
     *
     * @param $workspaceId
     * @param $scannerAppId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function storeWorkspaceApp($workspaceId, $scannerAppId)
    {
        $this->validate($this->request, $this->getValidationRules(), $this->getValidationMessages());

        $workspaceApp = new WorkspaceApp();
        $workspaceApp->setName($this->request->get('name'))
                     ->setDescription($this->request->get('description'));

        $command      = new CreateWorkspaceApp(intval($workspaceId), intval($scannerAppId), $workspaceApp);
        $workspaceApp = $this->sendCommandToBusHelper($command);

        if ($this->isCommandError($workspaceApp)) {
            return redirect()->back()->withInput();
        }

        $this->flashMessenger->success("New Workspace App created successfully.");
        return redirect()->route('workspace.view', ['workspaceId' => $workspaceId]);
    }

    /**
     * Update an existing WorkspaceApp
     *
     * @POST("/workspace/app/update/{workspaceAppId}", as="app.update",
     *     where={"workspaceAppId":"[0-9]+"})
     *
     * @param $workspaceAppId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function updateWorkspaceApp($workspaceAppId)
    {
        $this->validate($this->request, $this->getValidationRules(), $this->getValidationMessages());

        $command      = new EditWorkspaceApp(intval($workspaceAppId), $this->request->all());
        $workspaceApp = $this->sendCommandToBusHelper($command);

        if ($this->isCommandError($workspaceApp)) {
            return redirect()->back()->withInput();
        }

        $this->flashMessenger->success("Workspace App updated successfully.");
        return redirect()->route('workspace.view', ['workspaceId' => $workspaceApp->getWorkspaceId()]);
    }

    /**
     * Delete WorkspaceApp and all related data
     *
     * @GET("/workspace/app/delete/{workspaceId}/{workspaceAppId}", as="app.delete",
     *     where={"workspaceId":"[0-9]+","workspaceAppId":"[0-9]+"})
     *
     * @param $workspaceAppId
     * @param $workspaceId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function deleteWorkspaceApp($workspaceAppId, $workspaceId)
    {
        $command      = new DeleteWorkspaceApp(intval($workspaceAppId), true);
        $workspaceApp = $this->sendCommandToBusHelper($command);

        if ($this->isCommandError($workspaceApp)) {
            return redirect()->back();
        }

        $this->flashMessenger->success("Workspace App deleted successfully.");
        return redirect()->route('workspace.view', ['workspaceId' => $workspaceId]);
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
            'name.filled' => 'A name is required to create a new App and it does not seem like you entered one. '
                .'Please try again.',
        ];
    }
}