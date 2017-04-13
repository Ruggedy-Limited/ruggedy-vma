<?php
// Home
Breadcrumbs::register('home', function($breadcrumbs) {
    /** @var \DaveJamesMiller\Breadcrumbs\Generator $breadcrumbs */
    $breadcrumbs->push('Home', route('home'));
});

// Home > Workspace
Breadcrumbs::register('workspace', function($breadcrumbs, $workspace) {
    /** @var \DaveJamesMiller\Breadcrumbs\Generator $breadcrumbs */
    /** @var App\Entities\Workspace $workspace */
    $breadcrumbs->parent('home');
    $breadcrumbs->push(
        "Workspace: {$workspace->getName()}",
        route('workspace.view', ['workspaceId' => $workspace->getId()])
    );
});

// Home > Create Workspace
Breadcrumbs::register('createWorkspace', function($breadcrumbs) {
    /** @var \DaveJamesMiller\Breadcrumbs\Generator $breadcrumbs */
    $breadcrumbs->parent('home');
    $breadcrumbs->push('New Workspace', route('workspace.create'));
});

// Home > Edit Workspace
Breadcrumbs::register('editWorkspace', function($breadcrumbs, $workspace) {
    /** @var \DaveJamesMiller\Breadcrumbs\Generator $breadcrumbs */
    /** @var App\Entities\Workspace $workspace */
    $breadcrumbs->parent('workspace', $workspace);
    $breadcrumbs->push(
        "Edit Workspace: {$workspace->getName()}",
        route('workspace.edit', ['workspaceId' => $workspace->getId()])
    );
});

// Home > Workspace > Folder
Breadcrumbs::register('folder', function($breadcrumbs, $folder) {
    /** @var \DaveJamesMiller\Breadcrumbs\Generator $breadcrumbs */
    /** @var App\Entities\Folder $folder */
    $breadcrumbs->parent('workspace', $folder->getWorkspace());
    $breadcrumbs->push(
        "Folder: {$folder->getName()}",
        route('workspace.folder.view', ['folderId' => $folder->getId()])
    );
});

// Home > Workspace > Create Folder
Breadcrumbs::register('createFolder', function($breadcrumbs, $workspace) {
    /** @var \DaveJamesMiller\Breadcrumbs\Generator $breadcrumbs */
    /** @var App\Entities\Workspace $workspace */
    $breadcrumbs->parent('workspace', $workspace);
    $breadcrumbs->push(
        'New Folder',
        route('workspace.folder.create', ['workspaceId' => $workspace->getId()])
    );
});

// Home > Workspace > Edit Folder
Breadcrumbs::register('editFolder', function($breadcrumbs, $folder) {
    /** @var \DaveJamesMiller\Breadcrumbs\Generator $breadcrumbs */
    /** @var App\Entities\Folder $folder */
    $breadcrumbs->parent('folder', ['folderId' => $folder->getId()]);
    $breadcrumbs->push(
        'New Folder',
        route('workspace.folder.edit', ['folderId' => $folder->getId()])
    );
});

// Home > Workspace > WorkspaceApp
Breadcrumbs::register('workspaceApp', function($breadcrumbs, $workspaceApp) {
    /** @var \DaveJamesMiller\Breadcrumbs\Generator $breadcrumbs */
    /** @var App\Entities\WorkspaceApp $workspaceApp */
    $breadcrumbs->parent('workspace', $workspaceApp->getWorkspace());
    $breadcrumbs->push(
        "{$workspaceApp->getScannerApp()->getFriendlyName()} App: {$workspaceApp->getName()}",
        route('workspace.app.view', ['workspaceAppId' => $workspaceApp->getId()])
    );
});

// Home > Workspace > Apps
Breadcrumbs::register('workspaceApps', function($breadcrumbs, $workspace) {
    /** @var \DaveJamesMiller\Breadcrumbs\Generator $breadcrumbs */
    /** @var App\Entities\Workspace $workspace */
    $breadcrumbs->parent('workspace', $workspace);
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
        route('workspace.app.create', [
            'workspaceId'  => $workspace->getId(),
            'scannerAppId' => $scannerApp->getId()
        ])
    );
});

// Home > Workspace > Edit Workspace App
Breadcrumbs::register('editWorkspaceApp', function($breadcrumbs, $workspaceApp) {
    /** @var \DaveJamesMiller\Breadcrumbs\Generator $breadcrumbs */
    /** @var App\Entities\WorkspaceApp $workspaceApp */
    $breadcrumbs->parent('workspace', $workspaceApp->getWorkspace());
    $breadcrumbs->push(
        "Edit {$workspaceApp->getScannerApp()->getFriendlyName()} App: {$workspaceApp->getName()}",
        route('workspace.app.edit', ['workspaceAppId' => $workspaceApp->getId()])
    );
});

// Home > Workspace > WorkspaceApp > File
Breadcrumbs::register('file', function($breadcrumbs, $file) {
    /** @var \DaveJamesMiller\Breadcrumbs\Generator $breadcrumbs */
    /** @var App\Entities\File $file */
    $breadcrumbs->parent('workspace', $file->getWorkspaceApp()->getWorkspace());
    $breadcrumbs->push(
        "{$file->getWorkspaceApp()->getScannerApp()->getFriendlyName()} App: {$file->getWorkspaceApp()->getName()}",
        route('workspace.app.view', ['workspaceAppId' => $file->getWorkspaceApp()->getId()])
    );
    $breadcrumbs->push(
        "{$file->getPathBasename()}: {$file->getName()}",
        route('workspace.app.file.view', ['fileId' => $file->getId()])
    );
});

// Home > Workspace > WorkspaceApp > Add a File
Breadcrumbs::register('createFile', function($breadcrumbs, $workspaceApp) {
    /** @var \DaveJamesMiller\Breadcrumbs\Generator $breadcrumbs */
    /** @var App\Entities\WorkspaceApp $workspaceApp */
    $breadcrumbs->parent('workspace', $workspaceApp->getWorkspace());
    $breadcrumbs->push(
        "{$workspaceApp->getScannerApp()->getFriendlyName()} App: {$workspaceApp->getName()}",
        route('workspace.app.view', ['workspaceAppId' => $workspaceApp->getId()])
    );
    $breadcrumbs->push(
        "New File",
        route('workspace.app.file.form', ['workspaceAppId' => $workspaceApp->getId()])
    );
});

// Home > Workspace > WorkspaceApp > Edit File
Breadcrumbs::register('editFile', function($breadcrumbs, $file) {
    /** @var \DaveJamesMiller\Breadcrumbs\Generator $breadcrumbs */
    /** @var App\Entities\File $file */
    $breadcrumbs->parent('workspace', $file->getWorkspaceApp()->getWorkspace());
    $breadcrumbs->push(
        "{$file->getWorkspaceApp()->getScannerApp()->getFriendlyName()} App: {$file->getWorkspaceApp()->getName()}",
        route('workspace.app.view', ['workspaceAppId' => $file->getWorkspaceApp()->getId()])
    );
    $breadcrumbs->push(
        "Edit: {$file->getPathBasename()}: {$file->getName()}",
        route('workspace.app.file.edit', ['fileId' => $file->getId()])
    );
});

// Home > Workspace > WorkspaceApp > File > Vulnerability
Breadcrumbs::register('vulnerability', function($breadcrumbs, $vulnerability) {
    /** @var \DaveJamesMiller\Breadcrumbs\Generator $breadcrumbs */
    /** @var App\Entities\Vulnerability $vulnerability */
    $breadcrumbs->parent('workspace', $vulnerability->getFile()->getWorkspaceApp()->getWorkspace());
    $breadcrumbs->push(
        "{$vulnerability->getFile()->getWorkspaceApp()->getScannerApp()->getFriendlyName()} "
            . "App: {$vulnerability->getFile()->getWorkspaceApp()->getName()}",
        route('workspace.app.view', ['workspaceAppId' => $vulnerability->getFile()->getWorkspaceApp()->getId()])
    );
    $breadcrumbs->push(
        "{$vulnerability->getFile()->getPathBasename()}: {$vulnerability->getFile()->getName()}",
        route('workspace.app.file.view', ['fileId' => $vulnerability->getFile()->getId()])
    );
    $breadcrumbs->push(
        $vulnerability->getName(),
        route('vulnerability.view', ['vulnerabilityId' => $vulnerability->getId()])
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

// Home > Blog > [Category]
Breadcrumbs::register('category', function($breadcrumbs, $category) {
    /** @var \DaveJamesMiller\Breadcrumbs\Generator $breadcrumbs */
    $breadcrumbs->parent('blog');
    $breadcrumbs->push($category->title, route('category', $category->id));
});

// Home > Blog > [Category] > [Page]
Breadcrumbs::register('page', function($breadcrumbs, $page) {
    /** @var \DaveJamesMiller\Breadcrumbs\Generator $breadcrumbs */
    $breadcrumbs->parent('category', $page->category);
    $breadcrumbs->push($page->title, route('page', $page->id));
});