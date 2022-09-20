<?php

namespace Anastasia\Blog\Http\Actions\Users;

use Anastasia\Blog\Exceptions\HttpException;
use Anastasia\Blog\Exceptions\UserNotFoundException;
use Anastasia\Blog\Http\{Actions\ActionInterface, ErrorResponse, Request, Response, SuccessfulResponse};
use Anastasia\Blog\Repositories\UsersRepo\UsersRepositoryInterface;

class FindByUsername implements ActionInterface
{
    public function __construct(
        private UsersRepositoryInterface $usersRepository
    ) {
    }

    public function handle(Request $request): Response
    {
        try {
            $userName = $request->query('userName');
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }
        try {
            $user = $this->usersRepository->getByUserName($userName);
        } catch (UserNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }

        return new SuccessfulResponse([
            'userName' => $user->userName(),
            'name' => $user->name()->getFirstName() . ' ' . $user->name()->getLastName(),
        ]);
    }
}