<?php

namespace Anastasia\Blogs\UnitTests\Container;

class ClassDependingOnAnother
{
    public function __construct(
        private SomeClassWithoutDependencies $one,
        private SomeClassWithParameter $two,
    )
    {
    }
}