<?php

namespace Tests\Unit\Application\User;

use PHPUnit\Framework\TestCase;
use App\Application\User\Commands\RegisterUser\RegisterUserCommand;
use App\Application\User\Commands\RegisterUser\RegisterUserCommandHandler;
use App\Domain\User\Contracts\UserRepository;
use App\Domain\User\Entities\User;

class RegisterUserCommandHandlerTest extends TestCase
{
    public function test_register_user_creates_user_when_email_not_registered(): void
    {
        $repo = $this->createMock(UserRepository::class);

        // findByEmail harus dipanggil dan return null (belum ada)
        $repo->expects($this->once())
            ->method('findByEmail')
            ->with('test@example.com')
            ->willReturn(null);

        // create harus dipanggil sekali
        $repo->expects($this->once())
            ->method('create')
            ->with($this->callback(function (User $u) {
                $this->assertNull($u->id);
                $this->assertSame('Test', $u->name);
                $this->assertSame('test@example.com', $u->email);
                $this->assertNotEmpty($u->passwordHash);
                $this->assertStringStartsWith('$2y$', $u->passwordHash);

                return true;
            }))
            ->willReturnCallback(function (User $u) {
                return new User(
                    id: 123,
                    name: $u->name,
                    email: $u->email,
                    passwordHash: $u->passwordHash
                );
            });

        $handler = new RegisterUserCommandHandler($repo);

        $cmd = new RegisterUserCommand(
            name: 'Test',
            email: 'test@example.com',
            password: 'password123'
        );

        $result = $handler->handle($cmd);

        $this->assertSame(123, $result->id);
        $this->assertSame('Test', $result->name);
        $this->assertSame('test@example.com', $result->email);
    }

    public function test_register_user_throws_exception_when_email_already_registered(): void
    {
        $repo = $this->createMock(UserRepository::class);

        // findByEmail return user (berarti email sudah ada)
        $repo->expects($this->once())
            ->method('findByEmail')
            ->with('test@example.com')
            ->willReturn(new User(
                id: 1,
                name: 'Existing',
                email: 'test@example.com',
                passwordHash: 'hashed'
            ));

        // create tidak boleh dipanggil
        $repo->expects($this->never())
            ->method('create');

        $handler = new RegisterUserCommandHandler($repo);

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Email already registered');

        $handler->handle(new RegisterUserCommand(
            name: 'Test',
            email: 'test@example.com',
            password: 'password123'
        ));
    }
}