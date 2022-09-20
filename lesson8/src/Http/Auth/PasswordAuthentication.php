<?php

namespace Anastasia\Blog\Http\Auth;

use Anastasia\Blog\Blogs\User;
use Anastasia\Blog\Exceptions\{AuthException, HttpException, UserNotFoundException};
use Anastasia\Blog\Http\Request;
use Anastasia\Blog\Repositories\UsersRepo\UsersRepositoryInterface;

class PasswordAuthentication implements PasswordAuthenticationInterface
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
            $user = $this->usersRepository->getByUsername($userName);
        } catch (UserNotFoundException $e) {
            throw new AuthException($e->getMessage());
        }

        try {
            $password = $request->jsonBodyField('password');
        } catch (HttpException $e) {
            throw new AuthException($e->getMessage());
        }

        if(!$user->checkPassword($password)) {
            throw new AuthException('Wrong password');
        }

        return $user;
    }
}