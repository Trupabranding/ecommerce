<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Flexible search service that supports multiple backends:
 * - Default: Database LIKE queries (no external dependencies)
 * - Scout: Laravel Scout with Meilisearch backend (when configured)
 *
 * Usage:
 *   $results = app(GlobalSearchService::class)->search('query', ['Product', 'Order']);
 */
class GlobalSearchService
{
    public function __construct() {}

    /**
     * Search across multiple models using the best available backend.
     *
     * @param string $query Search query string
     * @param array<string> $models Fully qualified model class names
     * @param int $limit Maximum results per model
     * @return Collection<array{model: string, id: string, title: string, details: array}>
     */
    public function search(string $query, array $models, int $limit = 50): Collection
    {
        if (empty($query) || empty($models)) {
            return collect();
        }

        // Attempt Scout search first if available (and Meilisearch configured)
        if ($this->isScoutAvailable()) {
            return $this->searchWithScout($query, $models, $limit);
        }

        // Fallback to database LIKE queries
        return $this->searchWithDatabase($query, $models, $limit);
    }

    /**
     * Search using Laravel Scout + Meilisearch
     *
     * Requires:
     * - laravel/scout installed
     * - SCOUT_DRIVER=meilisearch configured
     * - Models using Searchable trait
     */
    private function searchWithScout(string $query, array $models, int $limit): Collection
    {
        $results = collect();

        foreach ($models as $modelClass) {
            if (! class_exists($modelClass)) {
                continue;
            }

            /** @var Model $model */
            $model = new $modelClass;

            // Check if model uses Searchable trait
            if (! method_exists($model, 'search')) {
                continue;
            }

            try {
                $scoutResults = $model::search($query)
                    ->take($limit)
                    ->get();

                foreach ($scoutResults as $result) {
                    $results->push([
                        'model' => class_basename($modelClass),
                        'id' => $result->getKey(),
                        'title' => $result->{$model->getKeyName()},
                        'details' => $this->getResultDetails($result),
                        'url' => $this->getResourceUrl($result),
                    ]);
                }
            } catch (\Throwable) {
                // Scout/Meilisearch not working, fall through to database
                return $this->searchWithDatabase($query, $models, $limit);
            }
        }

        return $results;
    }

    /**
     * Search using database LIKE queries (no external dependencies)
     *
     * Falls back to Filament's global searchable attributes for each model.
     */
    private function searchWithDatabase(string $query, array $models, int $limit): Collection
    {
        $results = collect();
        $searchTerm = "%{$query}%";

        foreach ($models as $modelClass) {
            if (! class_exists($modelClass)) {
                continue;
            }

            /** @var Model $model */
            $model = new $modelClass;

            // Get searchable attributes from Resource (Filament pattern)
            $resourceClass = $this->getResourceClass($modelClass);
            if (! $resourceClass || ! method_exists($resourceClass, 'getGloballySearchableAttributes')) {
                continue;
            }

            $searchableAttributes = $resourceClass::getGloballySearchableAttributes();
            if (empty($searchableAttributes)) {
                continue;
            }

            try {
                $modelResults = $this->buildSearchQuery($model, $searchableAttributes, $searchTerm)
                    ->limit($limit)
                    ->get();

                foreach ($modelResults as $result) {
                    $results->push([
                        'model' => class_basename($modelClass),
                        'id' => $result->getKey(),
                        'title' => $result->{$model->getKeyName()},
                        'details' => $this->getResultDetails($result),
                        'url' => $this->getResourceUrl($result),
                    ]);
                }
            } catch (\Throwable) {
                // Model query failed, skip
                continue;
            }
        }

        return $results;
    }

    /**
     * Build a query for searching across multiple columns with LIKE
     */
    private function buildSearchQuery(Model $model, array $attributes, string $searchTerm)
    {
        return $model->query()->where(function ($query) use ($attributes, $searchTerm) {
            foreach ($attributes as $attribute) {
                $query->orWhere($attribute, 'LIKE', $searchTerm);
            }
        });
    }

    /**
     * Get resource class name from model class name
     *
     * Convention: Domain\Shop\Product\Models\Product -> App\Filament\Admin\Resources\Shop\ProductResource
     */
    private function getResourceClass(string $modelClass): ?string
    {
        $modelBasename = class_basename($modelClass);
        $resourceClasses = [
            "App\\Filament\\Admin\\Resources\\Shop\\{$modelBasename}Resource",
            "App\\Filament\\Admin\\Resources\\Access\\{$modelBasename}Resource",
        ];

        foreach ($resourceClasses as $resourceClass) {
            if (class_exists($resourceClass)) {
                return $resourceClass;
            }
        }

        return null;
    }

    /**
     * Get additional details for search result (delegates to Resource)
     */
    private function getResultDetails(Model $record): array
    {
        $resourceClass = $this->getResourceClass(get_class($record));
        if ($resourceClass && method_exists($resourceClass, 'getGlobalSearchResultDetails')) {
            return $resourceClass::getGlobalSearchResultDetails($record) ?? [];
        }

        return [];
    }

    /**
     * Get URL to view/edit result in admin
     */
    private function getResourceUrl(Model $record): ?string
    {
        $resourceClass = $this->getResourceClass(get_class($record));
        if ($resourceClass && method_exists($resourceClass, 'getUrl')) {
            try {
                return $resourceClass::getUrl('edit', ['record' => $record]) ??
                       $resourceClass::getUrl('view', ['record' => $record]);
            } catch (\Throwable) {
                return null;
            }
        }

        return null;
    }

    /**
     * Check if Scout is available and configured
     */
    private function isScoutAvailable(): bool
    {
        // Scout not available if package not installed
        if (! class_exists(\Laravel\Scout\Scout::class)) {
            return false;
        }

        // Only use Scout if Meilisearch driver is configured
        $driver = config('scout.driver', null);
        if ($driver !== 'meilisearch') {
            return false;
        }

        // Check if Meilisearch is reachable
        try {
            $config = config('meilisearch');
            if (! $config || ! isset($config['host'])) {
                return false;
            }

            return true;
        } catch (\Throwable) {
            return false;
        }
    }
}
