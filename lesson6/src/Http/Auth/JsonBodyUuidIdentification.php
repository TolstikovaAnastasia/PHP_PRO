<?php

namespace Anastasia\Blog\Http\Auth;

use Anastasia\Blog\Blogs\{UUID, User};
use Anastasia\Blog\Exceptions\{HttpException, AuthException, InvalidArgumentException, UserNotFoundException};
use Anastasia\Blog\Http\Request;
use Anastasia\Blog\Repositories\UsersRepo\UsersRepositoryInterface;

class JsonBodyUuidIdentification implements IdentificationInterface
{
    public function __construct(
        private UsersRepositoryInterface $usersRepository
    )
    {
    }

    /**
     * @throws AuthException
     */
    public function user(Request $request): User
    {
        try {
            $userUuid = new UUID($request->jsonBodyField('user_uuid'));
        } catch (HttpException|InvalidArgumentException $e) {
            throw new AuthException($e->getMessage());
        }

        try {
            return $this->usersRepository->get($userUuid);
        } catch (UserNotFoundException $e) {
            throw new AuthException($e->getMessage());
        }
    }
}