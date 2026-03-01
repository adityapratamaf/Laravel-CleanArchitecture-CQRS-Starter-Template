<?php

namespace App\Application\User\Commands\RegisterUser;

class RegisterUserCommand
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
    ) {}
}