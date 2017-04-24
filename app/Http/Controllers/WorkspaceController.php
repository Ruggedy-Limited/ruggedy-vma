<?php

namespace App\Http\Controllers;

use App\Commands\CreateWorkspace;
use App\Commands\DeleteWorkspace;
use App\Commands\EditWorkspace;
use App\Commands\GetWorkspace;
use App\Entities\Workspace;
use App\Policies\ComponentPolicy;
use Auth;

/**
 * @Middleware("web")
 */
class WorkspaceController extends AbstractController
{
    /**
     * Show the form for creating a new resource.
     *
     * @GET("/workspace/create", as="workspace.create")
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (Auth::user()->cannot(ComponentPolicy::ACTION_EDIT, new Workspace())) {
            $this->flashMessenger->error("You do not have permission to create Workspaces.");
            return redirect()->back();
        }

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
        $entity->setName($this->request->get('name'))
            ->setDescription($this->request->get('description'));

        // Create a command and send it over the bus to the handler
        $command = new CreateWorkspace(intval($userId), $entity);
        $workspace = $this->sendCommandToBusHelper($command);

        if ($this->isCommandError($workspace)) {
            return redirect()->back()->withInput();
        }

        $this->flashMessenger->success("Workspace created successfully.");
        return redirect()->route('home');
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
        $workspace = $this->sendCommandToBusHelper($command);

        if ($this->isCommandError($workspace)) {
            return redirect()->back()->withInput();
        }

        return view('workspaces.view', ['workspace' => $workspace]);
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
        $workspace = $this->sendCommandToBusHelper($command);

        if ($this->isCommandError($workspace)) {
            return redirect()->back();
        }

        if (Auth::user()->cannot(ComponentPolicy::ACTION_EDIT, $workspace)) {
            $this->flashMessenger->error("You do not have permission to edit that Workspace.");
            return redirect()->back();
        }

        return view('workspaces.edit', ['workspace' => $workspace]);
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
        $command   = new EditWorkspace(intval($workspaceId), $this->request->all());
        $workspace = $this->sendCommandToBusHelper($command);

        if ($this->isCommandError($workspace)) {
            return redirect()->back()->withInput();
        }

        $this->flashMessenger->success("Workspace updated successfully.");
        return redirect()->route('home');
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
        $command   = new DeleteWorkspace(intval($workspaceId), true);
        $workspace = $this->sendCommandToBusHelper($command);

        if ($this->isCommandError($workspace)) {
            return redirect()->back();
        }

        // Add a success message and then generate a response for the Controller
        $this->flashMessenger->success("Workspace deleted successfully.");
        return redirect()->route('home');
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
            'name.required' => 'You must give the Workspace a name.',
        ];
    }
}

