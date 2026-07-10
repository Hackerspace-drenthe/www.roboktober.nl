<?php

declare(strict_types=1);

namespace App\Services\Analytics;

class PathNormalizer
{
    public function normalizePath(?string $path, bool $excludeAdmin = false): ?string
    {
        if (! is_string($path) || $path === '') {
            return null;
        }

        $normalizedPath = parse_url($path, PHP_URL_PATH);

        if (! is_string($normalizedPath) || $normalizedPath === '') {
            return null;
        }

        if (str_starts_with($normalizedPath, '/api')) {
            return null;
        }

        if ($excludeAdmin && str_starts_with($normalizedPath, '/admin')) {
            return null;
        }

        return $normalizedPath;
    }
}
