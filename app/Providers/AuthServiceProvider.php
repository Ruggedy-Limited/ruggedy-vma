<?php

namespace App\Providers;

use App;
use App\Auth\CustomUserProvider;
use App\Entities\Asset;
use App\Entities\Comment;
use App\Entities\File;
use App\Entities\Folder;
use App\Entities\ScannerApp;
use App\Entities\OpenPort;
use App\Entities\User;
use App\Entities\Vulnerability;
use App\Entities\VulnerabilityReferenceCode;
use App\Entities\Workspace;
use App\Entities\WorkspaceApp;
use App\Policies\ComponentPolicy;
use Doctrine\Instantiator\Exception\InvalidArgumentException;
use Doctrine\ORM\EntityManager;
use DoctrineProxies\__CG__\App\Entities\Asset as AssetProxy;
use DoctrineProxies\__CG__\App\Entities\Comment as CommentProxy;
use DoctrineProxies\__CG__\App\Entities\File as FileProxy;
use DoctrineProxies\__CG__\App\Entities\Vulnerability as VulnerabilityProxy;
use DoctrineProxies\__CG__\App\Entities\VulnerabilityReferenceCode as VulnerabilityReferenceCodeProxy;
use DoctrineProxies\__CG__\App\Entities\OpenPort as OpenPortProxy;
use DoctrineProxies\__CG__\App\Entities\ScannerApp as ScannerAppProxy;
use DoctrineProxies\__CG__\App\Entities\WorkspaceApp as WorkspaceAppProxy;
use DoctrineProxies\__CG__\App\Entities\Folder as FolderProxy;
use DoctrineProxies\__CG__\App\Entities\User as UserProxy;
use DoctrineProxies\__CG__\App\Entities\Workspace as WorkspaceProxy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Auth;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        User::class                            => ComponentPolicy::class,
        UserProxy::class                       => ComponentPolicy::class,
        Workspace::class                       => ComponentPolicy::class,
        WorkspaceProxy::class                  => ComponentPolicy::class,
        Asset::class                           => ComponentPolicy::class,
        AssetProxy::class                      => ComponentPolicy::class,
        Vulnerability::class                   => ComponentPolicy::class,
        VulnerabilityProxy::class              => ComponentPolicy::class,
        VulnerabilityReferenceCode::class      => ComponentPolicy::class,
        VulnerabilityReferenceCodeProxy::class => ComponentPolicy::class,
        OpenPort::class                        => ComponentPolicy::class,
        OpenPortProxy::class                   => ComponentPolicy::class,
        ScannerApp::class                      => ComponentPolicy::class,
        ScannerAppProxy::class                 => ComponentPolicy::class,
        WorkspaceApp::class                    => ComponentPolicy::class,
        WorkspaceAppProxy::class               => ComponentPolicy::class,
        Folder::class                          => ComponentPolicy::class,
        FolderProxy::class                     => ComponentPolicy::class,
        Comment::class                         => ComponentPolicy::class,
        CommentProxy::class                    => ComponentPolicy::class,
        File::class                            => ComponentPolicy::class,
        FileProxy::class                       => ComponentPolicy::class,
    ];

    /**
     * Register any application authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Auth::provider('custom', function ($app, array $config) {
            // Return an instance of Illuminate\Contracts\Auth\UserProvider...
            $entityClass = $config['model'];
            $entity = new $entityClass();

            /** @var EntityManager $em */
            $em = $app['registry']->getManagerForClass($entityClass);
            /** @var App\Repositories\UserRepository $repository */
            $repository = $em->getRepository($entityClass);

            if (!$em || !$repository) {
                throw new InvalidArgumentException("No EntityManager is set-up for {$entity}");
            }

            return new CustomUserProvider($entity, $app['hash'], $repository);
        });
    }
}
