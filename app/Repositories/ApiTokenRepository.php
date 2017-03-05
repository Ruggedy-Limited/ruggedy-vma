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
        $queryBuilder = $this->createQueryBuilder('at')
            ->addSelect('u')
            ->leftJoin('at.' . ApiToken::USER, 'u');

        $criteria = Criteria::create()->andWhere(
            Criteria::expr()->eq(ApiToken::TOKEN, $token)
        )->andWhere(
            Criteria::expr()->orX(
                Criteria::expr()->isNull(ApiToken::EXPIRES_AT),
                Criteria::expr()->gte(ApiToken::EXPIRES_AT, Carbon::now())
            )
        )->setMaxResults(1);

        return $queryBuilder->addCriteria($criteria)
            ->getQuery()
            ->getOneOrNullResult();
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
            ApiToken::ID         => Uuid::uuid4(),
            ApiToken::USER       => $user,
            ApiToken::NAME       => $name,
            ApiToken::TOKEN      => str_random(60),
            ApiToken::METADATA   => $data,
            ApiToken::TRANSIENT  => false,
            ApiToken::EXPIRES_AT => null,
        ]);

        $this->_em->persist($newToken);
        $this->_em->flush($newToken);

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
            ApiToken::NAME     => $name,
            ApiToken::METADATA => $metadata,
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
                $this->_em->remove($token);
            }
        });

        $this->_em->flush();
    }

    /**
     * @param User $user
     * @return Collection
     */
    public function all($user)
    {
        return collect($user->getApiTokens()->toArray());
    }
}