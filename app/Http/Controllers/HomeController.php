<?php

namespace App\Http\Controllers;

use App\Commands\GetListOfWorkspaces;

/**
 * @Middleware({"web", "auth"})
 */
class HomeController extends AbstractController
{
    /**
     * Show the application dashboard.
     *
     * @GET("/", as="home")
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $command = new GetListOfWorkspaces(0);
        $workspaces = $this->sendCommandToBusHelper($command);
        return view('home', ['workspaces' => $workspaces]);
    }

    public function theme()
    {
        return view('theme');
    }

    /**
     * @inheritdoc
     *
     * @return array
     */
    protected function getValidationRules(): array
    {
        return [];
    }

    /**
     * @inheritdoc
     *
     * @return array
     */
    protected function getValidationMessages(): array
    {
        return [];
    }
}
