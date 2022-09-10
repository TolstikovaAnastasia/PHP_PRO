<?php

namespace Anastasia\Blog\Http\Auth;

use Anastasia\Blog\Blogs\User;
use Anastasia\Blog\Exceptions\{AuthException, AuthTokenNotFoundException, HttpException};
use Anastasia\Blog\Http\Request;
use Anastasia\Blog\Repositories\AuthTokensRepository\AuthTokensRepositoryInterface;
use Anastasia\Blog\Repositories\UsersRepo\UsersRepositoryInterface;
use DateTimeImmutable;

class BearerTokenAuthentication implements TokenAuthenticationInterface
{
    private const HEADER_PREFIX = 'Bearer ';

    public function __construct(
        private AuthTokensRepositoryInterface $authTokensRepository,
        private UsersRepositoryInterface $usersRepository,
    )
    {
    }

    /**
     * @throws AuthException
     */
    public function user(Request $request): User
    {
        try {
            $header = $request->header('Authorization');
        } catch (HttpException $e) {
            throw new AuthException($e->getMessage());
        }

        if (!str_starts_with($header, self::HEADER_PREFIX)) {
            throw new AuthException("Malformed token: [$header]");
        }
        $token = mb_substr($header, strlen(self::HEADER_PREFIX));

        try {
            $authToken = $this->authTokensRepository->get($token);
        } catch (AuthTokenNotFoundException) {
            throw new AuthException("Bad token: [$token]");
        }

        if ($authToken->expiresOn() <= new DateTimeImmutable()) {
            throw new AuthException("Token expired: [$token]");
        }

        $userUuid = $authToken->userUuid();
        return $this->usersRepository->get($userUuid);
    }
}