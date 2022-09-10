<?php

namespace Anastasia\Blog\Http\Actions\Auth;

use Anastasia\Blog\Blogs\AuthToken;
use Anastasia\Blog\Exceptions\AuthException;
use Anastasia\Blog\Http\Actions\ActionInterface;
use Anastasia\Blog\Http\Auth\PasswordAuthenticationInterface;
use Anastasia\Blog\Http\{Request, Response, ErrorResponse, SuccessfulResponse};
use Anastasia\Blog\Repositories\AuthTokensRepository\AuthTokensRepositoryInterface;
use DateTimeImmutable;

class LogIn implements ActionInterface
{
    public function __construct(
        private PasswordAuthenticationInterface $passwordAuthentication,
        private AuthTokensRepositoryInterface $authTokensRepository
    )
    {
    }

    public function handle(Request $request): Response
    {
        try {
            $user = $this->passwordAuthentication->user($request);
        } catch (AuthException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $authToken = new AuthToken(
            bin2hex(random_bytes(40)),
            $user->uuid(),
            (new DateTimeImmutable())->modify('+1 day')
        );

        $this->authTokensRepository->save($authToken);

        return new SuccessfulResponse([
            'token' => (string)$authToken->token(),
        ]);
    }
}