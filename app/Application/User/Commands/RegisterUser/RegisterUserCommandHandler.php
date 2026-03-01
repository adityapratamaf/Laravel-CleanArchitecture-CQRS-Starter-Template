<?php

namespace App\Application\User\Commands\RegisterUser;

use App\Domain\User\Contracts\UserRepository;
use App\Domain\User\Entities\User;

class RegisterUserCommandHandler
{
    public function __construct(private UserRepository $users) {}

    public function handle(RegisterUserCommand $command): User
    {
        if ($this->users->findByEmail($command->email)) {
            throw new \DomainException('Email already registered');
        }

        $user = new User(
            id: null,
            name: $command->name,
            email: $command->email,
            passwordHash: password_hash($command->password, PASSWORD_BCRYPT),
        );

        return $this->users->create($user);
    }
}