<?php

namespace Anastasia\Blogs\UnitTests\Container;

use Anastasia\Blog\Blogs\Container\DIContainer;
use Anastasia\Blog\Exceptions\NotFoundException;
use Anastasia\Blog\Repositories\UsersRepo\InMemoryUsersRepo;
use Anastasia\Blog\Repositories\UsersRepo\UsersRepositoryInterface;
use PHPUnit\Framework\TestCase;

class DIContainerTest extends TestCase
{
    /**
     * @throws NotFoundException
     */
    public function testItResolvesClassWithDependencies(): void
    {
        $container = new DIContainer();

        $container->bind(
            SomeClassWithParameter::class,
            new SomeClassWithParameter(42)
        );

        $object = $container->get(ClassDependingOnAnother::class);
        $this->assertInstanceOf(
            ClassDependingOnAnother::class,
            $object
        );
    }
    /**
     * @throws NotFoundException
     */
    public function testItReturnsPredefinedObject(): void
    {
        $container = new DIContainer();
        $container->bind(
            SomeClassWithParameter::class,
            new SomeClassWithParameter(42)
        );

        $object = $container->get(SomeClassWithParameter::class);
        $this->assertInstanceOf(
            SomeClassWithParameter::class,
            $object
        );

        $this->assertSame(42, $object->value());
    }

    /**
     * @throws NotFoundException
     */
    public function testItResolvesClassByContract(): void
    {
        $container = new DIContainer();

        $container->bind(
            UsersRepositoryInterface::class,
            InMemoryUsersRepo::class
        );

        $object = $container->get(UsersRepositoryInterface::class);
        $this->assertInstanceOf(
            InMemoryUsersRepo::class,
            $object
        );
    }

    /**
     * @throws NotFoundException
     */
    public function testItResolvesClassWithoutDependencies(): void
    {
        $container = new DIContainer();
        $object = $container->get(SomeClassWithoutDependencies::class);
        $this->assertInstanceOf(
            SomeClassWithoutDependencies::class,
            $object
        );

    }

    /*public function testItThrowsAnExceptionIfCannotResolveType(): void
    {
        $container = new DIContainer();
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage(
            'Cannot resolve type: Anastasia\Blog\Blogs\tests\Container\SomeClass'
        );

        $container->get(SomeClass::class);
    }*/
}