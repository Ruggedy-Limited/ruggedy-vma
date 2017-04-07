<?php

namespace App\Http\Controllers;

use App\Commands\CreateUser;
use App\Commands\EditUserAccount;
use App\Commands\GetAllUsers;
use App\Commands\GetUser;
use App\Entities\User;
use App\Http\Responses\ErrorResponse;
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
        $command = new GetAllUsers(0);
        $users   = $this->sendCommandToBusHelper($command);
        return $this->controllerResponseHelper($users, 'settings.index', ['users' => $users]);
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
            $response = new ErrorResponse(
                $this->validator()->getValidatorFor($user)->messages()->first()
            );

            return $this->controllerResponseHelper($response,'settings.user.create', [],true);
        }

        event(new Registered($user));
        $command = new CreateUser(
            0,
            $user->setName($this->request->get('name'))
                ->setEmail($this->request->get('email'))
                ->setPassword(bcrypt($this->request->get('password')))
        );

        $user = $this->sendCommandToBusHelper($command);
        return $this->controllerResponseHelper($user, 'settings.view', [], true);
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
     * @GET("/settings/user/edit/{userId}", as="settings.user.edit", where={"userId":"[0-9]+"})
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit($id)
    {
        $command = new GetUser(intval($id));
        $user    = $this->sendCommandToBusHelper($command);

        return $this->controllerResponseHelper($user, 'settings.usersEdit', ['user' => $user]);
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
            $this->addMessage(
                $this->validator()->getValidatorFor($user)->messages()->first(),
                    parent::MESSAGE_TYPE_ERROR
            );
            return $this->controllerResponseHelper(null,'settings.user.edit', ['userId' => $id], true);
        }

        $command = new EditUserAccount(intval($id), $this->request->all());
        $user    = $this->sendCommandToBusHelper($command);

        $this->addMessage("User details updated successfully.", parent::MESSAGE_TYPE_SUCCESS);
        return $this->controllerResponseHelper($user, 'settings.user.edit', ['userId' => $id], true);
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
        return $this->controllerResponseHelper(null, 'settings.view', [], true);
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
        return $this->controllerResponseHelper($user, 'settings.usersEdit', ['user' => $user]);
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
            User::NAME         => 'bail|filled',
            User::EMAIL        => 'bail|filled|email',
            User::PASSWORD     => 'bail|present',
            'password-confirm' => 'bail|required_with:' . User::PASSWORD . '|same:' . User::PASSWORD,
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
            User::NAME         => 'Please enter a valid name.',
            User::EMAIL        => 'Please enter a valid email address.',
            User::PASSWORD     => 'Please enter a valid password.',
            'password-confirm' => 'The passwords entered are not the same',
        ];
    }
}
