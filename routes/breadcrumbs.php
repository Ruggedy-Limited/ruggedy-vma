<?php
// Home
Breadcrumbs::register('home', function($breadcrumbs) {
    /** @var \DaveJamesMiller\Breadcrumbs\Generator $breadcrumbs */
    $breadcrumbs->push('Home', route('home'));
});

Breadcrumbs::register('search', function($breadcrumbs, $searchTerm) {
    /** @var \DaveJamesMiller\Breadcrumbs\Generator $breadcrumbs */
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Search results for "' . $searchTerm . '"');
});

// Home > Workspace > Apps
Breadcrumbs::register('workspaceApps', function($breadcrumbs, $workspace) {
    /** @var \DaveJamesMiller\Breadcrumbs\Generator $breadcrumbs */
    /** @var App\Entities\Workspace $workspace */
    $breadcrumbs->parent('home');
    $breadcrumbs->push(
        "Workspace: {$workspace->getName()}",
        route('workspace.view', ['workspaceId' => $workspace->getId()])
    );
    $breadcrumbs->push(
        'Choose an App to Add',
        route('workspace.apps', ['workspaceId' => $workspace->getId()])
    );
});

// Home > Workspace > Create WorkspaceApp
Breadcrumbs::register('createWorkspaceApp', function($breadcrumbs, $workspace, $scannerApp) {
    /** @var \DaveJamesMiller\Breadcrumbs\Generator $breadcrumbs */
    /** @var App\Entities\Workspace $workspace */
    /** @var App\Entities\ScannerApp $scannerApp */
    $breadcrumbs->parent('workspaceApps', $workspace);
    $breadcrumbs->push(
        "New {$scannerApp->getFriendlyName()} App",
        route('app.create', [
            'workspaceId'  => $workspace->getId(),
            'scannerAppId' => $scannerApp->getId()
        ])
    );
});

// Home > Settings
Breadcrumbs::register('settings', function($breadcrumbs) {
    /** @var \DaveJamesMiller\Breadcrumbs\Generator $breadcrumbs */
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Settings', route('settings.view'));
});

// Home > Settings > New User
Breadcrumbs::register('newUser', function($breadcrumbs) {
    /** @var \DaveJamesMiller\Breadcrumbs\Generator $breadcrumbs */
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Settings', route('settings.view'));
    $breadcrumbs->push('New User', 'settings.user.create');
});

// Home > Settings > Edit User
Breadcrumbs::register('editUser', function($breadcrumbs, $user) {
    /** @var \DaveJamesMiller\Breadcrumbs\Generator $breadcrumbs */
    /** @var App\Entities\User $user */
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Settings', route('settings.view'));
    $breadcrumbs->push("Edit User: {$user->getName()}", route('settings.user.edit', ['userId' => $user->getId()]));
});

// Home > My Profile
Breadcrumbs::register('profile', function($breadcrumbs) {
    /** @var \DaveJamesMiller\Breadcrumbs\Generator $breadcrumbs */
    $breadcrumbs->parent('home');
    $breadcrumbs->push('My Profile', route('settings.user.profile'));
});

Breadcrumbs::register('dynamic', function($breadcrumbs, $param1 = null) {
    /** @var \DaveJamesMiller\Breadcrumbs\Generator $breadcrumbs */
    /** @var App\Entities\Base\AbstractEntity|App\Contracts\SystemComponent $param1 */
    $breadcrumbs->parent('home');
    if ($param1 !== null) {
        App\Services\ComponentService::getOrderedComponentHierarchy($param1)
            ->each(function ($entity) use ($breadcrumbs) {
                 /** @var App\Entities\Base\AbstractEntity|App\Contracts\SystemComponent $entity */
                 // Skip the file part of the Ruggedy App breadcrumbs
                 if ($entity instanceof App\Entities\File && $entity->getWorkspaceApp()->isRuggedyApp()) {
                     return;
                 }

                 $breadcrumbText = '';
                 if ($entity instanceof App\Entities\WorkspaceApp) {
                     $breadcrumbText = $entity->getScannerApp()->getFriendlyName() . ' ';
                 }

                 if (!($entity instanceof App\Entities\WorkspaceApp && $entity->isRuggedyApp())) {
                     $breadcrumbText .= $entity->getDisplayName();
                 }

                 if (method_exists($entity, 'getName')) {
                     $breadcrumbText .= ": " . $entity->getName();
                 }

                 $breadcrumbs->push($breadcrumbText, route($entity->getRouteName() . '.view', [
                     $entity->getRouteParameterName() => $entity->getId()
                 ]));
            });
    }

    $breadcrumbText = App\Services\ComponentService::getBreadcrumbTextFromRoute();
    if (empty($breadcrumbText)) {
        return;
    }

    $breadcrumbs->push(
        $breadcrumbText,
        Route::current()->uri()
    );
});