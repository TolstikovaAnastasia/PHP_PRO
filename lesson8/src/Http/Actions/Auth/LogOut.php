<?php

namespace Anastasia\Blog\Http\Actions\Auth;

use Anastasia\Blog\Exceptions\{AuthException, AuthTokenNotFoundException, AuthTokensRepositoryException, HttpException};
use Anastasia\Blog\Http\{Actions\ActionInterface,
    Request,
    Response,
    SuccessfulResponse,
    ErrorResponse};
use Anastasia\Blog\Repositories\AuthTokensRepository\AuthTokensRepositoryInterface;

class LogOut implements ActionInterface
{
    private const HEADER_PREFIX = 'Bearer ';

    public function __construct(
        private AuthTokensRepositoryInterface $authTokensRepository
    )
    {
    }

    /**
     * @throws AuthException
     * @throws AuthTokensRepositoryException
     * @throws HttpException
     */
    public function handle(Request $request): Response
    {
        $header = $request->header('Authorization');

        if (!str_starts_with($header, self::HEADER_PREFIX)) {
            throw new AuthException("Malformed token: [$header]");
        }

        $token = mb_substr($header, strlen(self::HEADER_PREFIX));

        if(!$this->authTokenExists($token)) {
            return new ErrorResponse('Cannot find token: ' . $token);
        }

        try {
            $this->authTokenExists($token);
            $this->authTokensRepository->save($token(new \DateTimeImmutable())->format(\DateTimeInterface::ATOM));

        } catch (\PDOException $e) {
            throw new AuthTokensRepositoryException(
                $e->getMessage(), (int)$e->getCode(), $e
            );
        }

        return new SuccessfulResponse([
            'Token expired' => "[$token]"
        ]);
    }

    private function authTokenExists(string $token): bool
    {
        try {
            $this->authTokensRepository->get($token);
        } catch (AuthTokenNotFoundException $e) {
            return false;
        }
        return true;
    }
}