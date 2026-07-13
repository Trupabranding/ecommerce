# Global Search Implementation Guide

This document covers implementing flexible search in Herd eCommerce that works with multiple backends.

## Overview

The application supports two search backends:

1. **Default (Database LIKE)** — No external dependencies, works immediately
2. **Scout + Meilisearch** — 10x faster, requires setup (optional)

## Default: Database LIKE Search

Works out of the box with no configuration.

### How It Works

1. Filament `getGloballySearchableAttributes()` method defines searchable columns per Resource
2. `GlobalSearchService` builds LIKE queries across those columns
3. Returns results with details and URLs

### Example: Product Search

```php
// ProductResource.php
public static function getGloballySearchableAttributes(): array
{
    return [
        'name',
        'parent_sku',
        'brand.name',
        'skus.code',
    ];
}

// Usage
app(GlobalSearchService::class)->search('laptop', ['Domain\Shop\Product\Models\Product']);
```

### Performance (at scale)

| Dataset | Response Time | Notes |
|---------|--------------|-------|
| 1,000 products | 20-50ms | Fast, acceptable |
| 10,000 products | 100-200ms | Slowing down |
| 100,000 products | 500ms+ | Too slow (use Scout) |

**Recommendation:** Use default search up to 10k products. Beyond that, upgrade to Scout + Meilisearch.

---

## Optional: Scout + Meilisearch Setup

For faster search at scale (100k+ products).

### Prerequisites

- Docker or Meilisearch server running
- `laravel/scout` package
- `meilisearch/meilisearch-php` package

### 1. Install Packages

```bash
composer require laravel/scout meilisearch/meilisearch-php
php artisan vendor:publish --provider="Laravel\Scout\ScoutServiceProvider"
```

### 2. Configure .env

```env
SCOUT_DRIVER=meilisearch
MEILISEARCH_HOST=http://127.0.0.1:7700
MEILISEARCH_KEY=masterKey
```

### 3. Add Searchable Trait to Models

```php
// domain/Shop/Product/Models/Product.php
use Laravel\Scout\Searchable;

class Product extends Model
{
    use Searchable;

    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'parent_sku' => $this->parent_sku,
            'brand_name' => $this->brand->name ?? '',
            'sku_codes' => $this->skus->pluck('code')->join(' '),
            'created_at' => $this->created_at?->timestamp,
        ];
    }
}
```

### 4. Index Models

```bash
php artisan scout:import "Domain\Shop\Product\Models\Product"
php artisan scout:import "Domain\Shop\Order\Models\Order"
php artisan scout:import "Domain\Shop\Customer\Models\Customer"
```

### 5. Enable Automatic Indexing (Optional)

Add to `config/scout.php`:

```php
'after_commit' => true,  // Queue indexing after DB transaction
```

### 6. Monitor Indexing

```bash
# Check index status
php artisan tinker
>>> $product = Product::first();
>>> $product->searchable();  // Force reindex

# Check Meilisearch directly
curl http://127.0.0.1:7700/health
```

### 7. Test Search

```php
use App\Services\GlobalSearchService;

$service = app(GlobalSearchService::class);

// Automatic backend detection:
// - If Scout + Meilisearch configured → uses Meilisearch
// - Otherwise → falls back to database LIKE
$results = $service->search('laptop', [
    'Domain\Shop\Product\Models\Product',
    'Domain\Shop\Order\Models\Order',
]);

$results->each(function ($result) {
    echo "{$result['model']}: {$result['title']}";
});
```

### Performance Gains (Scout + Meilisearch)

| Dataset | Database LIKE | Meilisearch | Improvement |
|---------|--------------|-------------|-------------|
| 10,000 | 100-200ms | 20-30ms | 5-10x |
| 100,000 | 500ms+ | 40-60ms | 10-20x |
| 1,000,000 | 2-5s | 50-80ms | 25-100x |

### Cost/Benefit Analysis

**Meilisearch Overhead:**
- RAM: ~10-50MB for 100k products
- Disk: ~100-500MB for 100k products
- CPU: Minimal (background indexing)

**When to Use Meilisearch:**
- ✅ >10k searchable records
- ✅ Sub-100ms search requirement
- ✅ Heavy search usage
- ❌ Don't use for <5k records (overhead not worth it)
- ❌ Don't use if data changes infrequently

---

## Docker Compose (for local development)

```yaml
# docker-compose.yml
version: '3.8'

services:
  meilisearch:
    image: getmeili/meilisearch:latest
    ports:
      - "7700:7700"
    environment:
      MEILI_MASTER_KEY: masterKey
    volumes:
      - meilisearch_data:/meili_data

volumes:
  meilisearch_data:
```

Start with:
```bash
docker-compose up -d meilisearch
php artisan scout:import "Domain\Shop\Product\Models\Product"
```

---

## API Usage

### Basic Search

```php
$service = app(GlobalSearchService::class);

$results = $service->search('query', [
    'Domain\Shop\Product\Models\Product',
    'Domain\Shop\Order\Models\Order',
]);

// Returns: Collection of [model, id, title, details, url]
```

### With Custom Limit

```php
$results = $service->search('query', ['Domain\Shop\Product\Models\Product'], limit: 100);
```

### In Controllers

```php
// API endpoint
Route::get('/api/search', function (Request $request) {
    $service = app(GlobalSearchService::class);
    
    return response()->json(
        $service->search($request->query('q'), [
            'Domain\Shop\Product\Models\Product',
            'Domain\Shop\Order\Models\Order',
            'Domain\Shop\Customer\Models\Customer',
        ])->toArray()
    );
});
```

---

## Filament Global Search Integration

The service integrates with Filament's existing `getGloballySearchableAttributes()`:

```php
// In ProductResource
public static function getGloballySearchableAttributes(): array
{
    return [
        'name',
        'parent_sku',
        'brand.name',
        'skus.code',
    ];
}

// GlobalSearchService automatically uses these attributes
// No Resource changes needed!
```

---

## Troubleshooting

### Scout not detecting Meilisearch

Check configuration:
```bash
php artisan tinker
>>> config('scout.driver')  // Should be 'meilisearch'
>>> config('meilisearch')   // Should have host/key
```

### Models not searchable

Ensure model has `Searchable` trait:
```php
use Laravel\Scout\Searchable;

class Product extends Model {
    use Searchable;
}
```

### Meilisearch connection fails

```bash
# Test connection
curl http://127.0.0.1:7700/health

# Check .env
MEILISEARCH_HOST=http://127.0.0.1:7700
MEILISEARCH_KEY=masterKey
```

### Old data in search results

Reindex model:
```bash
php artisan scout:flush "Domain\Shop\Product\Models\Product"
php artisan scout:import "Domain\Shop\Product\Models\Product"
```

---

## Summary

| Feature | Database | Scout |
|---------|----------|-------|
| Setup Time | 5 minutes | 30 minutes |
| Performance | Good for <10k | Excellent for 100k+ |
| External Deps | None | Meilisearch server |
| Fallback | N/A | Automatic to database |
| Cost | Free | Free (optional) |

**Recommendation:** Start with database search, upgrade to Scout when you hit 10k+ searchable records.
