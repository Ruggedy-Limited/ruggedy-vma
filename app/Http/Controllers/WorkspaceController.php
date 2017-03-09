<?php

namespace App\Http\Controllers;

use App\Commands\CreateWorkspace;
use App\Commands\GetWorkspace;
use App\Entities\Workspace;
use App\Http\Responses\ErrorResponse;
use Auth;
use Illuminate\Http\Request;

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
     * @return \Illuminate\Http\Response
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

        // Handle error responses from the handler
        if ($response instanceof ErrorResponse) {
            $this->flashMessenger->error($response->getMessage());
        }

        // Redirect back to the Workspace listing
        return redirect()->route('home');
    }

    /**
     * Display the specified resource.
     *
     * @GET("/workspace/{workspaceId}", as="workspace.show", where={"workspaceId":"[0-9]+"})
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Create a command and send it over the bus to the handler
        $command = new GetWorkspace(intval($id));
        $response = $this->sendCommandToBusHelper($command);

        // Handle error responses from the handler and redirect back
        if ($response instanceof ErrorResponse) {
            $this->flashMessenger->error($response->getMessage());
            return redirect()->back();
        }

        // Redirect back to the Workspace listing
        return view('workspaces.view', ['workspace' => $response]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function apps()
    {
	return view ('workspaces.apps');
    }

    public function appsCreate()
    {
        return view ('workspaces.appsCreate');
    }

    public function app()
    {
        return view ('workspaces.app');
    }

    public function appShow()
    {
        return view ('workspaces.appShow');
    }

    public function appShowRecord()
    {
        return view ('workspaces.appShowRecord');
    }

    public function addFile() {
        return view ('workspaces.addFile');
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

