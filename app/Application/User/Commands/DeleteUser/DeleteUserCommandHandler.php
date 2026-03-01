<?php

namespace App\Application\User\Commands\DeleteUser;

use App\Domain\User\Contracts\UserRepository;

class DeleteUserCommandHandler
{
    public function __construct(private UserRepository $users) {}

    public function handle(DeleteUserCommand $command): void
    {
        $user = $this->users->findById($command->id);
        if (!$user) {
            throw new \DomainException('User not found');
        }

        $this->users->deleteById($command->id);
    }
}