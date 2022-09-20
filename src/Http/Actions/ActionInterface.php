<?php

namespace Anastasia\Blog\Http\Actions;

use Anastasia\Blog\Http\{Request, Response};

interface ActionInterface
{
    public function handle(Request $request): Response;
}