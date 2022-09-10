<?php

namespace Anastasia\Blog\Http\Auth;

use Anastasia\Blog\Blogs\User;
use Anastasia\Blog\Http\Request;

interface AuthenticationInterface
{
    public function user(Request $request): User;
}