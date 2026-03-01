<?php

namespace App\Application\User\Commands\DeleteUser;

class DeleteUserCommand
{
    public function __construct(public int $id) {}
}