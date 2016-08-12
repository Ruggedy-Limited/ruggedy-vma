<?php

namespace App\Providers;

use App\Auth\RuggedyTokenGuard;
use App\Entities\Asset;
use App\Entities\Project;
use App\Entities\ScannerApp;
use App\Entities\OpenPort;
use App\Entities\Team;
use App\Entities\User;
use App\Entities\Vulnerability;
use App\Entities\VulnerabilityReferenceCode;
use App\Entities\Workspace;
use App\Policies\ComponentPolicy;
use DoctrineProxies\__CG__\App\Entities\Project as ProjectProxy;
use DoctrineProxies\__CG__\App\Entities\Team as TeamProxy;
use DoctrineProxies\__CG__\App\Entities\User as UserProxy;
use DoctrineProxies\__CG__\App\Entities\Workspace as WorkspaceProxy;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        User::class                       => ComponentPolicy::class,
        UserProxy::class                  => ComponentPolicy::class,
        Team::class                       => ComponentPolicy::class,
        TeamProxy::class                  => ComponentPolicy::class,
        Project::class                    => ComponentPolicy::class,
        ProjectProxy::class               => ComponentPolicy::class,
        Workspace::class                  => ComponentPolicy::class,
        WorkspaceProxy::class             => ComponentPolicy::class,
        Asset::class                      => ComponentPolicy::class,
        Vulnerability::class              => ComponentPolicy::class,
        VulnerabilityReferenceCode::class => ComponentPolicy::class,
        OpenPort::class                   => ComponentPolicy::class,
        ScannerApp::class                 => ComponentPolicy::class,
    ];

    /**
     * Register any application authentication / authorization services.
     *
     * @param  \Illuminate\Contracts\Auth\Access\Gate  $gate
     * @return void
     */
    public function boot(GateContract $gate)
    {
        $this->registerPolicies($gate);

        // Send requests using the 'ruggedy' auth driver via the request driver
        Auth::viaRequest('ruggedy', function ($request) {
            return app(RuggedyTokenGuard::class)->user($request);
        });
    }
}
