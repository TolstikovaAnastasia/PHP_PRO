<?php

namespace Anastasia\Blog\Http\Auth;

use Anastasia\Blog\Blogs\User;
use Anastasia\Blog\Exceptions\{AuthException, HttpException, UserNotFoundException};
use Anastasia\Blog\Http\Request;
use Anastasia\Blog\Repositories\UsersRepo\UsersRepositoryInterface;

class JsonBodyUsernameIdentification implements IdentificationInterface
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
            $userName = $request->jsonBodyField('userName');
        } catch (HttpException $e) {
            throw new AuthException($e->getMessage());
        }

         try {
            return $this->usersRepository->getByUsername($userName);
        } catch (UserNotFoundException $e) {
            throw new AuthException($e->getMessage());
        }
    }
}