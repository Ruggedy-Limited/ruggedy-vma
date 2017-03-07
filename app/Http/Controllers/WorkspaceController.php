<?php

namespace App\Http\Controllers;

use App\Commands\CreateWorkspace;
use App\Entities\Workspace;
use App\Http\Responses\ErrorResponse;
use Auth;
use Illuminate\Http\Request;

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
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('workspaces.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $this->validate($this->request, $this->getValidationRules(), $this->getValidationMessages());

        $userId = Auth::user() ? Auth::user()->getId() : 0;

        $entity = new Workspace();
        $entity->setName($this->request->get('name'));
        $entity->setDescription($this->request->get('description'));

        $command = new CreateWorkspace($userId, $entity);
        $response = $this->sendCommandToBusHelper($command);

        if ($response instanceof ErrorResponse) {
            $this->flashMessenger->error($response->getMessage());
        }

        return redirect()->route('home');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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

