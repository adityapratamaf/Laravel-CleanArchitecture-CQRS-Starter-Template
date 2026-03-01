<?php

namespace App\Supports;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class Paginator
{
    /**
     * Paginate + meta tambahan (count, range) untuk API/WEB.
     *
     * @param  EloquentBuilder|QueryBuilder  $query
     */
    public static function paginateWithMeta(
        EloquentBuilder|QueryBuilder $query,
        int $perPage = 15,
        int $pageNumber = 1
    ): array {
        $pageNumber = max(1, $pageNumber);
        $perPage = min(max($perPage, 1), 100);

        /** @var LengthAwarePaginator $paginator */
        $paginator = $query->paginate($perPage, ['*'], 'page', $pageNumber);

        $items = $paginator->items();
        $count = count($items);
        $total = $paginator->total();

        $rangeStart = ($total === 0) ? 0 : (($paginator->currentPage() - 1) * $paginator->perPage()) + 1;
        $rangeEnd   = ($total === 0) ? 0 : min($rangeStart + $count - 1, $total);

        return [
            'data' => $items,
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page'    => $paginator->lastPage(),
                'per_page'     => $paginator->perPage(),
                'total'        => $total,
                'count'        => $count,        // jumlah item di halaman ini
                'range_start'  => $rangeStart,   // nomor item pertama di halaman ini
                'range_end'    => $rangeEnd,     // nomor item terakhir di halaman ini
            ],
        ];
    }
}