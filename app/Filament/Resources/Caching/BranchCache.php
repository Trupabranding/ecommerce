<?php

declare(strict_types=1);

namespace App\Filament\Resources\Caching;

use Domain\Shop\Branch\Models\Branch;
use Illuminate\Support\Collection;

class BranchCache
{
    private const CACHE_KEY = 'filament.branches.all';

    private const CACHE_TTL = 3600; // 1 hour

    public static function all(): Collection
    {
        return cache()->remember(self::CACHE_KEY, self::CACHE_TTL, fn () =>
            Branch::query()
                ->select('uuid', 'name', 'status')
                ->orderBy('name')
                ->get()
        );
    }

    public static function options(): Collection
    {
        return self::all()->pluck('name', 'uuid');
    }

    public static function invalidate(): void
    {
        cache()->forget(self::CACHE_KEY);
    }
}
