<?php

namespace App\Application\User\Queries\ListUsers;

class ListUsersQuery
{
    public int $page;
    public int $perPage;
    public ?string $search;

    public function __construct(int $page = 1, int $perPage = 15, ?string $search = null)
    {
        $this->page = max(1, $page);
        $this->perPage = min(max($perPage, 1), 100);
        $this->search = $search ? mb_strtolower(trim($search)) : null;
    }
}