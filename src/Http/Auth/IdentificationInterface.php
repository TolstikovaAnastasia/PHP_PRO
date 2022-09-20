<?php

namespace Anastasia\Blog\Http\Auth;

use Anastasia\Blog\Blogs\User;
use Anastasia\Blog\Http\Request;

interface IdentificationInterface
{
    public function user(Request $request): User;
}