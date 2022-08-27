<?php

namespace Anastasia\Blog\Http\Actions\Users;

use Anastasia\Blog\Exceptions\HttpException;
use Anastasia\Blog\Blogs\{Person\Name, User, UUID};
use Anastasia\Blog\Http\Actions\ActionInterface;
use Anastasia\Blog\Http\{ErrorResponse, Request, Response, SuccessfulResponse};
use Anastasia\Blog\Repositories\UsersRepo\UsersRepositoryInterface;

class CreateUser implements ActionInterface
{
    public function __construct(
        private UsersRepositoryInterface $usersRepository,
    ) {
    }

    public function handle(Request $request): Response
    {
        try {
            $newUserUuid = UUID::random();

            $user = new User(
                $newUserUuid,
                $request->jsonBodyField('userName'),
                new Name(
                    $request->jsonBodyField('firstName'),
                    $request->jsonBodyField('lastName')
                )
            );
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $this->usersRepository->save($user);

        return new SuccessfulResponse([
            'uuid' => (string)$newUserUuid,
        ]);
    }
}