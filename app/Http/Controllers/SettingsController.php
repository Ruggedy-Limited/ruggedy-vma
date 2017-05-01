<?php

namespace App\Http\Controllers;

use App\Commands\CreateUser;
use App\Commands\DeleteUser;
use App\Commands\EditUserAccount;
use App\Commands\GetAllUsers;
use App\Commands\GetUser;
use App\Entities\User;
use App\Policies\ComponentPolicy;
use App\Services\JsonLogService;
use Auth;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Translation\Translator;
use Laracasts\Flash\FlashNotifier;
use League\Tactician\CommandBus;
use Somnambulist\EntityValidation\Factories\EntityValidationFactory;

/**
 * @Middleware("web")
 */
class SettingsController extends AbstractController
{
    /** @var EntityValidationFactory */
    protected $validator;

    /**
     * SettingsController constructor.
     *
     * @param Request $request
     * @param Translator $translator
     * @param JsonLogService $logger
     * @param FlashNotifier $flashMessenger
     * @param CommandBus $bus
     * @param EntityValidationFactory $validator
     */
    public function __construct(
        Request $request, Translator $translator, JsonLogService $logger, FlashNotifier $flashMessenger,
        CommandBus $bus, EntityValidationFactory $validator)
    {
        parent::__construct($request, $translator, $logger, $flashMessenger, $bus);
        $this->validator = $validator;
    }

    /**
     * Display a listing of all Users in the system
     *
     * @GET("/settings", as="settings.view")
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function index()
    {
        if (Auth::user()->cannot(ComponentPolicy::ACTION_EDIT, new User())) {
            $this->flashMessenger->error("You do not have permission to view the application settings.");
            return redirect()->back();
        }

        $command = new GetAllUsers(0);
        $users   = $this->sendCommandToBusHelper($command);

        if ($this->isCommandError($users)) {
            return redirect()->back();
        }

        return view('settings.index', ['users' => $users]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @GET("/settings/users/create", as="settings.user.create")
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (Auth::user()->cannot(ComponentPolicy::ACTION_EDIT, new User())) {
            $this->flashMessenger->error("You do not have permission to create new Users.");
            return redirect()->back();
        }

        return view('settings.usersCreate');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @POST("/settings/user/store", as="settings.user.store")
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function store()
    {
        $user = new User();
        $user->setFromArray($this->request->all());
        if ($this->validator()->validate($user) === false) {
            $this->flashMessenger->error(
                $this->validator()->getValidatorFor($user)->messages()->first()
            );

            return redirect()->back()->withInput();
        }

        event(new Registered($user));
        $command = new CreateUser(
            0,
            $user->setName($this->request->get('name'))
                ->setEmail($this->request->get('email'))
                ->setPassword(bcrypt($this->request->get('password')))
                ->setIsAdmin($this->request->get('is_admin', false))
        );

        $user = $this->sendCommandToBusHelper($command);

        if ($this->isCommandError($user)) {
            return redirect()->back()->withInput();
        }

        $this->flashMessenger->success("New User created successfully.");
        return redirect()->route('settings.view');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @GET("/settings/user/edit/{userId}", as="settings.user.edit", where={"userId":"[0-9]+"})
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit($id)
    {
        $command = new GetUser(intval($id));
        $user    = $this->sendCommandToBusHelper($command);

        if ($this->isCommandError($user)) {
            return redirect()->back();
        }

        if (Auth::user()->cannot(ComponentPolicy::ACTION_EDIT, $user)) {
            $this->flashMessenger->error("You do not have permission to edit that User profile.");
            return redirect()->back();
        }

        return view('settings.usersEdit', ['user' => $user]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @POST("/settings/user/update/{userId}", as="settings.user.update", where={"userId":"[0-9]+"})
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function update($id)
    {
        $this->validate($this->request, $this->getValidationRules(), $this->getValidationMessages());

        $user = new User();
        $user->setFromArray($this->request->all());
        if ($this->validator()->validate($user) === false) {
            $this->flashMessenger->error(
                $this->validator()->getValidatorFor($user)->messages()->first()
            );

            return redirect()->back()->withInput();
        }

        $command = new EditUserAccount(intval($id), $this->request->all());
        $user    = $this->sendCommandToBusHelper($command);

        if ($this->isCommandError($user)) {
            return redirect()->back()->withInput();
        }

        $this->flashMessenger->success("User details updated successfully.");
        return redirect()->route('settings.user.edit', ['userId' => $id]);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @GET("/settings/user/delete/{userId}", as="settings.user.delete", where={"userId":"[0-9]+"})
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function destroy($id)
    {
        $command = new DeleteUser(intval($id));
        $user    = $this->sendCommandToBusHelper($command);
        if ($this->isCommandError($user)) {
            return redirect()->back();
        }

        $this->flashMessenger->success("User account deleted successfully.");
        return redirect()->route('settings.view');
    }

    /**
     * User profile editing form
     *
     * @GET("/settings/user/profile", as="settings.user.profile")
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function userProfile()
    {
        $user = Auth::user();
        return view('settings.usersEdit', ['user' => $user]);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @return EntityValidationFactory
     */
    protected function validator()
    {
        return $this->validator;
    }

    /**
     * @inheritdoc
     *
     * @return array
     */
    protected function getValidationRules(): array
    {
        return [
            'name'             => 'bail|required',
            'email'            => 'bail|required|email',
            'password'         => 'bail|present',
            'password-confirm' => 'bail|required_with:password|same:password',
            'is_admin'         => 'bail|nullable|bool',
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
            'name.required'    => 'A name for the User is required, but it does not seem like you entered one.',
            'email'            => [
                'required' => 'An email address is required for a User, but it does not seem like you entered one.',
                'email'    => 'The email address you entered does not seem to be a valid. Please check and try again.',
            ],
            'password-confirm' => [
                'required_with' => 'It does not seem like you confirmed your password. Please check and try again.',
                'same'          => 'The passwords you entered do not match. Please try again.',
            ],
        ];
    }
}
