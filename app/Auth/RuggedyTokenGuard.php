<?php

namespace App\Auth;

use App\Entities\ApiToken;
use App\Entities\User;
use App\Repositories\ApiTokenRepository;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use DateTime;
use Doctrine\ORM\EntityManager;
use Exception;
use Illuminate\Http\Request;
use Auth;
use Laravel\Spark\JWT;

class RuggedyTokenGuard
{
    /**
     * The token repository implementation.
     *
     * @var ApiTokenRepository
     */
    protected $tokens;

    /** @var UserRepository  */
    protected $userRepository;

    /** @var EntityManager */
    protected $em;

    /**
     * Create a new token guard instance.
     *
     * @param  ApiTokenRepository $tokens
     * @param UserRepository $userRepository
     * @param EntityManager $em
     */
    public function __construct(ApiTokenRepository $tokens, UserRepository $userRepository, EntityManager $em)
    {
        $this->tokens         = $tokens;
        $this->userRepository = $userRepository;
        $this->em             = $em;
    }

    /**
     * Get the authenticated user for the given request.
     *
     * @param  Request  $request
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function user(Request $request)
    {
        if (Auth::user()) {
            return $this->alreadyHasUser();
        }

        if (! $token = $this->getToken($request)) {
            return null;
        }

        // If the token is valid we will return the user instance that is associated with
        // the token as well as populate the token usage time. If a token wasn't found
        // of course this method will return null and no user will be authenticated.
        Auth::setDefaultDriver('api');

        if ($this->em->contains($token)) {
            $token->setLastUsedAt(new DateTime());
            $this->em->persist($token);
            $this->em->flush($token);
        }

        /** @var User $user */
        $user = $token->getUser();
        $user->addApiToken($token);
        
        return $user;
    }

    /**
     * Return the current user with a fresh transient token.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable
     */
    protected function alreadyHasUser()
    {
        return Auth::user()->setToken(
            $this->createTransientToken(Auth::id(), Carbon::now()->addMinutes(5))
        );
    }

    /**
     * Get the token instance from the database.
     *
     * @param  Request  $request
     * @return ApiToken
     */
    protected function getToken(Request $request)
    {
        $token = $this->getTokenFromRequest($request);

        if ($token instanceof ApiToken) {
            return $token;
        }

        return $token ? $this->tokens->validToken($token) : null;
    }

    /**
     * Get the token for the given request.
     *
     * @param  Request  $request
     * @return ApiToken|string
     */
    protected function getTokenFromRequest(Request $request)
    {
        $bearer = $request->bearerToken();

        // First we will check to see if the token is in the request input data or is a bearer
        // token on the request. If it is, we will consider this the token, otherwise we'll
        // look for the token in the cookies then attempt to validate that it is correct.
        $token = $request->input('api_token', $bearer);
        if (isset($token)) {
            return $token;
        }

        if (!$request->cookie('spark_token')) {
            return null;
        }

        return $this->getTokenFromCookie($request);
    }

    /**
     * Get the token for the given request cookie.
     *
     * @param  Request  $request
     * @return ApiToken|null
     */
    protected function getTokenFromCookie($request)
    {
        // If we need to retrieve the token from the cookie, it'll be encrypted so we must
        // first decrypt the cookie and then attempt to find the token value within the
        // database. If we can't decrypt the value we'll bail out with a null return.
        try {
            $token = JWT::decode(decrypt($request->cookie('spark_token')));
        } catch (Exception $e) {
            return null;
        }

        // We will compare the XSRF token in the decoded API token against the XSRF header
        // sent with the request. If the two don't match then this request is sent from
        // a valid source and we won't authenticate the request for further handling.
        if (! $this->validXsrf($token, $request)) {
            return null;
        }

        // Here we will create a token instance from the JWT token. This'll be a transient
        // token which allows all operations since the user is physically logged into a
        // screen of the application. We'll check the expiration date then return it.
        $token = $this->createTransientToken(
            $token['sub'], Carbon::createFromTimestamp($token['expiry'])
        );

        if (empty($token)) {
            return null;
        }

        $tokenExpiry = Carbon::instance($token->getExpiresAt());
        return Carbon::now()->gte($tokenExpiry) ? null : $token;
    }

    /**
     * Create a new transient token instance for the given user.
     *
     * @param  int  $userId
     * @param  Carbon  $expiration
     * @return ApiToken
     */
    protected function createTransientToken($userId, Carbon $expiration)
    {
        $user = $this->userRepository->find($userId);
        if (empty($user)) {
            return null;
        }

        $token = new ApiToken();
        return $token->setFromArray([
            'user'       => $user,
            'expires_at' => $expiration,
        ]);
    }

    /**
     * Determine if the XSRF / header are valid and match.
     *
     * @param $token
     * @param $request
     * @return bool
     */
    protected function validXsrf($token, $request)
    {
        return isset($token['xsrf']) && hash_equals(
            $token['xsrf'], (string) $this->decryptXsrfHeader($request)
        );
    }

    /**
     * Decrypt the XSRF header on the given request.
     *
     * @param  Request  $request
     * @return string|null
     */
    protected function decryptXsrfHeader($request)
    {
        try {
            return decrypt($request->header('X-XSRF-TOKEN'));
        } catch (Exception $e) {
            return null;
        }
    }
}