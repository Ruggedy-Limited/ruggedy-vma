<?php

namespace App\Http\Controllers;

use App\Commands\CreateComment;
use App\Commands\CreateWorkspace;
use App\Commands\CreateWorkspaceApp;
use App\Commands\DeleteComment;
use App\Commands\DeleteWorkspace;
use App\Commands\DeleteWorkspaceApp;
use App\Commands\EditComment;
use App\Commands\EditFile;
use App\Commands\EditWorkspace;
use App\Commands\EditWorkspaceApp;
use App\Commands\GetFile;
use App\Commands\GetListOfScannerApps;
use App\Commands\GetNewComments;
use App\Commands\GetScannerApp;
use App\Commands\GetVulnerability;
use App\Commands\GetWorkspace;
use App\Commands\GetWorkspaceApp;
use App\Commands\UploadScanOutput;
use App\Entities\Comment;
use App\Entities\File;
use App\Entities\Folder;
use App\Entities\Workspace;
use App\Entities\WorkspaceApp;
use Auth;
use Doctrine\Common\Collections\Collection;
use Exception;

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
            return redirect()->back()->withInput();
        }

        return view('workspaces.create', ['workspace' => $workspace]);
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

        if ($this->isCommandError($scannerApps)) {
            return redirect()->back();
        }

        return view('workspaces.apps', [
            'scannerApps' => $scannerApps,
            'workspaceId' => $workspaceId,
        ]);
    }

    /**
     * Display the form to create a WorkspaceApp
     *
     * @GET("/workspace/app/create/{workspaceId}/{scannerAppId}", as="workspace.app.create",
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

        if ($this->isCommandError($scannerApp)) {
            return redirect()->back();
        }

        return view('workspaces.appsCreate', [
            'workspaceId'  => $workspaceId,
            'scannerAppId' => $scannerAppId,
            'scannerApp'   => $scannerApp,
        ]);
    }

    /**
     * Display the form to edit a Workspace App
     *
     * @GET("/workspace/app/edit/{workspaceAppId}", as="workspace.app.edit", where={"workspaceAppId":"[0-9]+"})
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
     * @POST("/workspace/app/store/{workspaceId}/{scannerAppId}", as="workspace.app.store",
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
     * @POST("/workspace/app/update/{workspaceAppId}", as="workspace.app.update",
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
     * Get a WorkspaceApp and related data
     *
     * @GET("/workspace/app/{workspaceAppId}", as="workspace.app.view", where={"workspaceAppId":"[0-9]+"})
     *
     * @param $workspaceAppId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function viewWorkspaceApp($workspaceAppId)
    {
        $command      = new GetWorkspaceApp(intval($workspaceAppId));
        $workspaceApp = $this->sendCommandToBusHelper($command);

        if ($this->isCommandError($workspaceApp)) {
            return redirect()->back();
        }

        return view('workspaces.app', ['workspaceApp' => $workspaceApp]);
    }

    /**
     * Delete WorkspaceApp and all related data
     *
     * @GET("/workspace/app/delete/{workspaceId}/{workspaceAppId}", as="workspace.app.delete",
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
     * Get a single Vulnerability record related to a specific file
     *
     * @GET("/vulnerability/{vulnerabilityID}", as="vulnerability.view", where={"vulnerabilityId":"[0-9]+"})
     *
     * @param $vulnerabilityId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function appShowRecord($vulnerabilityId)
    {
        $command       = new GetVulnerability(intval($vulnerabilityId));
        $vulnerability = $this->sendCommandToBusHelper($command);

        if ($this->isCommandError($vulnerability)) {
            return redirect()->back();
        }

        return view('workspaces.appShowRecord', [
            'vulnerability' => $vulnerability,
            'folders'       => $this->getFoldersForSelect(
                $vulnerability->getFile()->getWorkspaceApp()->getWorkspace()->getFolders()
            ),
            'assets'        => $vulnerability->getAssets(),
            'comments'      => $vulnerability->getComments(),
        ]);
    }

    /**
     * Show a file and related data including Assets, Vulnerabilities and Comments
     *
     * @GET("/workspace/app/file/{fileId}", as="workspace.app.file.view", where={"fileId":"[0-9]+"})
     *
     * @param $fileId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function viewFile($fileId)
    {
        $command  = new GetFile(intval($fileId));
        $fileInfo = $this->sendCommandToBusHelper($command);

        if ($this->isCommandError($fileInfo)) {
            return redirect()->back();
        }

        return view('workspaces.app-show', $fileInfo);
    }

    /**
     * Display the form where scan output files can be uploaded
     *
     * @GET("/workspace/app/file/create/{workspaceAppId}", as="workspace.app.file.form",
     *     where={"workspaceAppId":"[0-9]+"})
     *
     * @param $workspaceAppId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function addFile($workspaceAppId)
    {
        $command      = new GetWorkspaceApp(intval($workspaceAppId));
        $workspaceApp = $this->sendCommandToBusHelper($command);

        if ($this->isCommandError($workspaceApp)) {
            return redirect()->back();
        }

        return view('workspaces.addFile', ['workspaceApp' => $workspaceApp]);
    }

    /**
     * Upload scanner output for processing
     *
     * @POST("/workspace/app/file/upload/{workspaceAppId}", as="workspace.app.file.upload",
     *     where={"workspaceAppId":"[0-9]+"})
     *
     * @param $workspaceAppId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function uploadFile($workspaceAppId)
    {
        // Initialise a new File entity to send with the command
        $file = new File();
        $file->setName($this->request->get('name'))
            ->setDescription($this->request->get('description'));

        // Create and send the command to upload the scanner output file
        $command  = new UploadScanOutput(intval($workspaceAppId), $this->request->file('file'), $file);
        $file     = $this->sendCommandToBusHelper($command);

        if ($this->isCommandError($file)) {
            return redirect()->back()->withInput();
        }

        return redirect()->route('workspace.app.view', ['workspaceApp' => $file->getWorkspaceApp()->getId()]);
    }

    /**
     * Display the form for editing a file's name and description
     *
     * @GET("/workspace/app/file/edit/{fileId}", as="workspace.app.file.edit", where={"fileId":"[0-9]+"})
     *
     * @param $fileId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function editFile($fileId)
    {
        $command = new GetFile(intval($fileId));
        $file    = $this->sendCommandToBusHelper($command);

        if ($this->isCommandError($file)) {
            return redirect()->back();
        }

        return view('workspaces.edit-file', ['file' => $file]);
    }

    /**
     * Display the form for editing a file's name and description
     *
     * @POST("/workspace/app/file/update/{fileId}", as="workspace.app.file.update", where={"fileId":"[0-9]+"})
     *
     * @param $fileId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function updateFile($fileId)
    {
        $this->validate($this->request, $this->getValidationRules(), $this->getValidationMessages());

        $command  = new EditFile($fileId, $this->request->all());
        $file     = $this->sendCommandToBusHelper($command);

        if ($this->isCommandError($file)) {
            return redirect()->back()->withInput();
        }

        return redirect()->route('workspace.app.file.view', ['fileId' => $fileId]);
    }

	/**
	 * Create a new comment
	 *
	 * @POST("/comment/create/{vulnerabilityId}", as="comment.create", where={"vulnerabilityId":"[0-9]+"})
	 *
	 * @param $vulnerabilityId
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
	 */
    public function createComment($vulnerabilityId)
    {
    	$comment = new Comment();
    	$comment->setContent($this->request->get('comment'));

    	$command = new CreateComment(intval($vulnerabilityId), $comment);
    	$comment = $this->sendCommandToBusHelper($command);

        if ($this->isCommandError($comment)) {
            return redirect()->back()->withInput();
        }

    	$this->flashMessenger->success("A new comment was posted successfully.");
        return redirect()->route('vulnerability.view', ['vulnerabilityId' => $vulnerabilityId]);
    }

	/**
	 * Edit an existing comment
	 *
	 * @POST("/comment/edit/{commentId}", as="comment.edit", where={"commentId":"[0-9]+"})
	 *
	 * @param $commentId
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
	 */
    public function editComment($commentId)
    {
    	$command = new EditComment(intval($commentId), [
    		Comment::CONTENT => $this->request->get('comment-' . $commentId)
	    ]);

    	$comment = $this->sendCommandToBusHelper($command);

        if ($this->isCommandError($comment)) {
            return redirect()->back()->withInput();
        }

    	$this->flashMessenger->success("Comment updated successfully.");
    	return redirect()->back();
    }

	/**
	 * Delete an existing comment
	 *
	 * @GET("/comment/delete/{commentId}", as="comment.remove", where={"commentId":"[0-9]+"})
	 *
	 * @param $commentId
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
	 */
    public function deleteComment($commentId)
    {
    	$command = new DeleteComment($commentId, true);
    	$comment = $this->sendCommandToBusHelper($command);

        if ($this->isCommandError($comment)) {
            return redirect()->back();
        }

    	$this->flashMessenger->success("Comment deleted successfully.");
        return redirect()->back();
    }

    /**
     * Get new comments
     *
     * @POST("/comments/updated/{vulnerabilityId}", as="comments.get.updated", where={"vulnerabilityId":"[0-9]+"})
     *
     * @param $vulnerabilityId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     * @throws Exception
     */
    public function getNewComments($vulnerabilityId)
    {
        $command = new GetNewComments(
            $vulnerabilityId,
            $this->request->get('newer-than', '0000-00-00 00:00:00')
        );

        $comments = $this->sendCommandToBusHelper($command);

        if ($this->isCommandError($comments)) {
            throw new Exception($comments->getMessage());
        }

        return view('partials.comment', ['comments' => $comments]);
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
     * Convert an ArrayCollection of Folder entities into an array of Folder names indexed by the Folder ID
     *
     * @param Collection $folders
     * @return array
     */
    protected function getFoldersForSelect(Collection $folders): array
    {
        return collect($folders->toArray())->reduce(function ($foldersForSelect, $folder) {
            /** @var Folder $folder */
            $foldersForSelect[$folder->getId()] = $folder->getName();
            return $foldersForSelect;
        }, []);
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

