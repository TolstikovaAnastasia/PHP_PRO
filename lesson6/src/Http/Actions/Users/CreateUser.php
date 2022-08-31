<?php

namespace Anastasia\Blog\Http\Actions\Users;

use Anastasia\Blog\Exceptions\HttpException;
use Anastasia\Blog\Blogs\{Person\Name, User, UUID};
use Anastasia\Blog\Http\Actions\ActionInterface;
use Anastasia\Blog\Http\{ErrorResponse, Request, Response, SuccessfulResponse};
use Anastasia\Blog\Repositories\UsersRepo\UsersRepositoryInterface;
use Psr\Log\LoggerInterface;

class CreateUser implements ActionInterface
{
    public function __construct(
        private UsersRepositoryInterface $usersRepository,
        private LoggerInterface $logger,
    )
    {
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

            $this->usersRepository->save($user);
            $this->logger->warning("Something goes wrong with user: $newUserUuid");

        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        return new SuccessfulResponse([
            'uuid' => (string)$newUserUuid,
        ]);
    }
}