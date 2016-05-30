<?php

namespace App\Repositories;

use App\Entities\ApiToken;
use App\Entities\User;
use Carbon\Carbon;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Illuminate\Support\Collection;
use Laravel\Spark\Contracts\Repositories\TokenRepository;
use Ramsey\Uuid\Uuid;
use Laravel\Spark\JWT;
use Laravel\Spark\Token;


class ApiTokenRepository extends EntityRepository implements TokenRepository
{
    /**
     * {@inheritdoc}
     */
    public function validToken($token)
    {
        $queryBuilder = $this->createQueryBuilder('at');
        $criteria = Criteria::create()->andWhere(
            Criteria::expr()->eq('token', $token)
        )->andWhere(
            Criteria::expr()->orX(
                Criteria::expr()->isNull('expires_at'),
                Criteria::expr()->gte('expires_at', Carbon::now())
            )
        )->setMaxResults(1);

        $queryBuilder->addCriteria($criteria);

        $result = $queryBuilder->getQuery()->getResult();
        return $result[0] ?? null;
    }

    /**
     * @param User $user
     * @param string $name
     * @param array $data
     * @return mixed
     */
    public function createToken($user, $name, array $data = [])
    {
        $this->deleteExpiredTokens($user);
        
        $newToken = new ApiToken();
        $newToken->setFromArray([
            'id'         => Uuid::uuid4(),
            'user'       => $user,
            'name'       => $name,
            'token'      => str_random(60),
            'metaData'   => $data,
            'transient'  => false,
            'expiresAt'  => null,
        ]);

        $this->getEntityManager()->persist($newToken);
        $this->getEntityManager()->flush($newToken);

        return $user->addApiToken($newToken);
    }

    /**
     * @param User $user
     * @return mixed
     */
    public function createTokenCookie($user)
    {
        $token = JWT::encode([
            'sub'    => $user->getId(),
            'xsrf'   => csrf_token(),
            'expiry' => Carbon::now()->addMinutes(5)->getTimestamp(),
        ]);

        return cookie(
            'spark_token', $token, 5, null,
            config('session.domain'), config('session.secure'), true
        );
    }

    /**
     * {@inheritdoc}
     */
    public function updateToken(Token $token, $name, array $abilities = [])
    {
        $metadata = $token->metadata;

        $metadata['abilities'] = $abilities;

        $token->forceFill([
            'name' => $name,
            'metadata' => $metadata,
        ])->save();
    }

    /**
     * @param User $user
     */
    public function deleteExpiredTokens($user)
    {
        $tokens = $user->getApiTokens();
        $tokenCollection = new Collection($tokens->toArray());
        $tokenCollection->each(function($token, $key)
        {
            /** @var ApiToken $token */
            if ($token->getExpiresAt() <= Carbon::now()) {
                $this->getEntityManager()->remove($token);
            }
        });

        $this->getEntityManager()->flush();
    }
}