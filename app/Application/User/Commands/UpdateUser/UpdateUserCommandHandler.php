<?php

namespace App\Application\User\Commands\UpdateUser;

use App\Domain\User\Contracts\UserRepository;
use App\Domain\User\Entities\User;

class UpdateUserCommandHandler
{
    public function __construct(private UserRepository $users) {}

    public function handle(UpdateUserCommand $command): User
    {
        $existing = $this->users->findById($command->id);
        if (!$existing) {
            throw new \DomainException('User not found');
        }

        $emailOwner = $this->users->findByEmail($command->email);
        if ($emailOwner && $emailOwner->id !== $command->id) {
            throw new \DomainException('Email already used');
        }

        $existing->name = $command->name;
        $existing->email = $command->email;

        if ($command->password !== null && $command->password !== '') {
            $existing->passwordHash = password_hash($command->password, PASSWORD_BCRYPT);
        }

        return $this->users->update($existing);
    }
}