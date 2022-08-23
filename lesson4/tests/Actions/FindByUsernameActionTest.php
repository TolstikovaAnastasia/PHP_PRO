<?php

namespace Actions;

use Anastasia\Blog\Blogs\Person\Name;
use Anastasia\Blog\Blogs\User;
use Anastasia\Blog\Blogs\UUID;
use Anastasia\Blog\Exceptions\UserNotFoundException;
use Anastasia\Blog\Http\Actions\Users\FindByUsername;
use Anastasia\Blog\Http\ErrorResponse;
use Anastasia\Blog\Http\Request;
use Anastasia\Blog\Http\SuccessfulResponse;
use Anastasia\Blog\Repositories\UsersRepo\UsersRepositoryInterface;
use PHPUnit\Framework\TestCase;

class FindByUsernameActionTest extends TestCase
{
    public function testItReturnsErrorResponseIfNoUsernameProvided(): void
    {
        $request = new Request([], []);
        $usersRepository = $this->usersRepository([]);
        $action = new FindByUsername($usersRepository);
        $response = $action->handle($request);
        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->expectOutputString('{"success":false,"reason":"No such query param in the request: userName"}');
        $response->send();
    }
    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */

    public function testItReturnsErrorResponseIfUserNotFound(): void
    {
        $request = new Request(['userName' => 'ivan'], []);
        $usersRepository = $this->usersRepository([]);
        $action = new FindByUsername($usersRepository);
        $response = $action->handle($request);
        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->expectOutputString('{"success":false,"reason":"Not found"}');
        $response->send();
    }
    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */

    public function testItReturnsSuccessfulResponse(): void
    {
        $request = new Request(['userName' => 'ivan'], []);

        $usersRepository = $this->usersRepository([
            new User(
                UUID::random(),
                'ivan',
                new Name('Ivan', 'Nikitin')
            ),
        ]);

        $action = new FindByUsername($usersRepository);
        $response = $action->handle($request);
        $this->assertInstanceOf(SuccessfulResponse::class, $response);
        $this->expectOutputString('{"success":true,"data":{"userName":"ivan","name":"Ivan Nikitin"}}');
        $response->send();
    }

    private function usersRepository(array $users): UsersRepositoryInterface
    {
        return new class($users) implements UsersRepositoryInterface {
            public function __construct(
                private array $users
            )
            {
            }

            public function save(User $user): void
            {
            }

            public function get(UUID $uuid): User
            {
                throw new UserNotFoundException("Not found");
            }

            public function getByUserName(string $userName): User
            {
                foreach ($this->users as $user) {
                    if ($user instanceof User && $userName === $user->userName())
                    {
                        return $user;
                    }
                }
                throw new UserNotFoundException("Not found");
            }
        };
    }
}