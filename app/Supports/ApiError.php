<?php

namespace App\Supports;

class ApiError
{
    public static function make(
        string $message,
        int $status,
        ?string $code = null,
        array $errors = [],
        array $meta = []
    ): array {
        return [
            'message' => $message,
            'code' => $code ?? self::defaultCode($status),
            'errors' => (object) $errors,
            'meta' => (object) $meta,
        ];
    }

    private static function defaultCode(int $status): string
    {
        return match (true) {
            $status === 401 => 'UNAUTHENTICATED',
            $status === 403 => 'FORBIDDEN',
            $status === 404 => 'NOT_FOUND',
            $status === 422 => 'VALIDATION_ERROR',
            default => 'SERVER_ERROR',
        };
    }
}