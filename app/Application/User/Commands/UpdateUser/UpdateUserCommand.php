<?php

namespace App\Application\User\Commands\UpdateUser;

class UpdateUserCommand
{
    public function __construct(
        public int $id,
        public string $name,
        public string $email,
        public ?string $password = null,
    ) {}
}