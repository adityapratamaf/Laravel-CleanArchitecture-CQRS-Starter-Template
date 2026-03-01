<?php

namespace App\Domain\User\Contracts;

use App\Domain\User\Entities\User;

interface UserRepository
{
    public function create(User $user): User;
    public function update(User $user): User;
    public function findById(int $id): ?User;
    public function findByEmail(string $email): ?User;
    public function deleteById(int $id): void;
}