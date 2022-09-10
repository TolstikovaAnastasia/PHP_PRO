<?php

namespace Anastasia\Blog\Repositories\AuthTokensRepository;

use Anastasia\Blog\Blogs\AuthToken;

interface AuthTokensRepositoryInterface
{
    public function save(AuthToken $authToken): void;
    public function get(string $token): AuthToken;
}