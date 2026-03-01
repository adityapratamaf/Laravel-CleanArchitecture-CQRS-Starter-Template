<?php

namespace App\Application\User\Queries\ListUsers;

use Illuminate\Support\Facades\DB;
use App\Supports\Paginator;

class ListUsersQueryHandler
{
    public function handle(ListUsersQuery $query): array
    {
        $qb = DB::table('users')
            ->select('id', 'name', 'email', 'created_at')
            ->orderByDesc('id');

        if ($query->search) {
            $s = mb_strtolower(trim($query->search));
            // Case Insensitive Search
            $qb->where(function ($w) use ($s) {
                $w->whereRaw('LOWER(name) LIKE ?', ["%{$s}%"])
                  ->orWhereRaw('LOWER(email) LIKE ?', ["%{$s}%"]);
            });
        }

        return Paginator::paginateWithMeta(
            query: $qb,
            perPage: $query->perPage,
            pageNumber: $query->page
        );
    }
}