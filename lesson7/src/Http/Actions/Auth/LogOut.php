<?php

namespace Anastasia\Blog\Http\Actions\Auth;

use Anastasia\Blog\Exceptions\{AuthException, AuthTokenNotFoundException, AuthTokensRepositoryException, HttpException};
use Anastasia\Blog\Http\{Actions\ActionInterface,
    Request,
    Response,
    SuccessfulResponse,
    ErrorResponse};
use Anastasia\Blog\Repositories\AuthTokensRepository\AuthTokensRepositoryInterface;
use PDO;

class LogOut implements ActionInterface
{
    private const HEADER_PREFIX = 'Bearer ';

    public function __construct(
        private PDO $connection,
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

        $expired = (new \DateTimeImmutable())->format(\DateTimeInterface::ATOM);

        try {
            $statement = $this->connection->prepare(
                'UPDATE tokens SET expires_on = :expired WHERE token = :token'
            );

            $statement->execute(
                [
                    ':expired' => $expired,
                    ':token' => $token
                ]
            );
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