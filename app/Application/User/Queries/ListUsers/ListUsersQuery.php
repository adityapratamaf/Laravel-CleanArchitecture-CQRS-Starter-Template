<?php

namespace App\Application\User\Queries\ListUsers;

class ListUsersQuery
{
    public int $page;
    public int $perPage;
    public ?string $search;
    public string $sortBy;
    public string $sortDir;

    public function __construct(
        int $page = 1,
        int $perPage = 15,
        ?string $search = null,
        string $sortBy = 'id',
        string $sortDir = 'desc'
    ) {
        $this->page = max(1, $page);
        $this->perPage = min(max($perPage, 1), 100);
        $this->search = $search ? mb_strtolower(trim($search)) : null;

        // whitelist column sort_by
        $allowedSort = ['id', 'name', 'email', 'created_at'];
        $this->sortBy = in_array($sortBy, $allowedSort, true) ? $sortBy : 'id';
        $this->sortDir = strtolower($sortDir) === 'asc' ? 'asc' : 'desc';
    }
}