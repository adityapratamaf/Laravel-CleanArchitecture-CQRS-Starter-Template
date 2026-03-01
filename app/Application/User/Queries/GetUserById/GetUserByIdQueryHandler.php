<?php

namespace App\Application\User\Queries\GetUserById;

use App\Domain\User\Contracts\UserRepository;

class GetUserByIdQueryHandler
{
    public function __construct(private UserRepository $users) {}

    public function handle(GetUserByIdQuery $query): array
    {
        $user = $this->users->findById($query->id);
        if (!$user) {
            throw new \DomainException('User not found');
        }

        // Query side boleh return array/viewmodel ringan (bukan entity)
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ];
    }
}