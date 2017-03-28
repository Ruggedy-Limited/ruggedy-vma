<?php

namespace App\Http\Controllers;

use App\Commands\CreateUser;
use App\Commands\GetAllUsers;
use App\Entities\User;
use Illuminate\Http\Request;

/**
 * @Middleware("web")
 */
class SettingsController extends AbstractController
{
    /**
     * Display a listing of all Users in the system
     *
     * @GET("/settings", as="settings.view")
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function index()
    {
        $command = new GetAllUsers(0);
        $users   = $this->sendCommandToBusHelper($command);
        return $this->controllerResponseHelper($users, 'settings.index', ['users' => $users]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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

    public function userCreate()
    {
        return view ('settings.usersCreate');
    }

    public function userEdit()
    {
        return view ('settings.usersEdit');
    }

    public function userProfile()
    {
        return view ('settings.profileEdit');
    }
}
