<?php

/*
 * Copyright (c) 2026 Trupa Technologies
 * All rights reserved.
 *
 * Developed by Boncanca Collins
 * GitHub: @iamtomc, @boncanca
 * Organization: trupabranding
 *
 * 1. Usage Permissions
 *    This software is proprietary to Trupa Technologies. The following restrictions apply:
 *    ✅ Allowed:
 *
 *     - Private use within the authorized organization.
 *     - Internal modifications.
 *     🚫 Not Allowed:
 *
 *     - Redistribution, sublicensing, or public sharing.
 *     - Commercial use outside of the authorized organization.
 * 2. Disclaimer of Warranty
 *    This software is provided "as is", without any warranty of any kind, express or implied, including but not limited to:
 *
 *     - Merchantability
 *     - Fitness for a particular purpose
 *     - Non-infringement
 * 3. Liability Limitation
 *    Under no circumstances shall the author(s) or copyright holders be liable for any claims, damages, or other liabilities arising from the use of this software.
 *
 * 4. Legal Enforcement
 *    Unauthorized use, distribution, or modification is strictly prohibited and may result in legal consequences.
 *
 * 📩 For inquiries, contact: hello@trupabranding.com
 * 🌐 Official Website: https://trupabranding.com
 * 📱 GitHub Organization: https://github.com/trupabranding
 */

declare(strict_types=1);

namespace Domain\Shop\Category\Models;

use Domain\Shop\Category\Database\Factories\CategoryFactory;
use Domain\Shop\Category\Models\EloquentBuilder\CategoryEloquentBuilder;
use Domain\Shop\Category\Observers\CategoryObserver;
use Domain\Shop\Category\Policies\CategoryPolicy;
use Domain\Shop\Product\Models\EloquentBuilder\ProductEloquentBuilder;
use Domain\Shop\Product\Models\Product;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\UseEloquentBuilder;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\HasBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * @property string $uuid
 * @property string|null $parent_uuid
 * @property string $name
 * @property string|null $description
 * @property bool $is_visible
 * @property array|null $configuration JSON configuration with display_rules, product_rules, pricing_rules, inventory_rules, seo_rules
 * @property int $order_column manage by spatie/eloquent-sortable
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Domain\Shop\Category\Models\Category> $children
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read string $name_with_parent
 * @property-read \Domain\Shop\Category\Models\Category|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Domain\Shop\Product\Models\Product> $products
 *
 * @mixin \Eloquent
 */
#[
    UseEloquentBuilder(CategoryEloquentBuilder::class),
    UsePolicy(CategoryPolicy::class),
    ObservedBy(CategoryObserver::class),
    UseFactory(CategoryFactory::class),
]
class Category extends Model implements HasMedia, Sortable
{
    /** @use HasBuilder<CategoryEloquentBuilder> */
    use HasBuilder;

    /** @use HasFactory<CategoryFactory> */
    use HasFactory;

    use HasUuids;
    use InteractsWithMedia;
    use LogsActivity;
    use SoftDeletes;
    use SortableTrait;

    #[\Override]
    protected $primaryKey = 'uuid';

    #[\Override]
    protected $fillable = [
        'parent_uuid',
        'name',
        'description',
        'is_visible',
        'configuration',
    ];

    #[\Override]
    protected function casts(): array
    {
        return [
            'is_visible' => 'bool',
            'configuration' => 'array',
        ];
    }

    /** @return Attribute<non-falsy-string, never> */
    protected function nameWithParent(): Attribute
    {
        return Attribute::get(
            fn (): string => sprintf(
                '%s > %s',
                $this->parent->name ?? trans('unknown'),
                $this->name
            )
        );
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany<\Domain\Shop\Category\Models\Category, $this> */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_uuid');
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\Domain\Shop\Category\Models\Category, $this> */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_uuid');
    }

    #[\Override]
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    #[\Override]
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')
            ->singleFile()
            ->useFallbackUrl(asset('images/no-image.webp'))
            ->registerMediaConversions(function () {
                $this->addMediaConversion('list')
                    ->fit(Fit::Fill, 240, 210);
                $this->addMediaConversion('thumb')
                    ->fit(Fit::Fill, 40, 40);
            });
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany<\Domain\Shop\Product\Models\Product, $this> */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function loadProductCountWithTrashed(): self
    {
        return $this->loadCount([
            'products' => function (ProductEloquentBuilder $query) {
                $query->withTrashed();
            },
        ]);
    }

    public function getConfiguration(): array
    {
        return $this->configuration ?? $this->defaultConfiguration();
    }

    private function defaultConfiguration(): array
    {
        return [
            'display_rules' => [
                'template' => 'default',
                'show_product_count' => true,
                'image_requirements' => [
                    'required_count' => 1,
                    'min_dimensions' => '300x300',
                ],
            ],
            'product_rules' => [
                'require_description' => true,
                'require_images' => 1,
                'required_attributes' => [],
                'custom_fields' => [],
            ],
            'pricing_rules' => [
                'tax_rate_override' => null,
                'allow_bulk_discounts' => false,
                'min_price_threshold' => null,
            ],
            'inventory_rules' => [
                'track_by_sku' => true,
                'allow_backorders' => false,
                'low_stock_threshold_override' => null,
                'show_stock_status' => true,
            ],
            'seo_rules' => [
                'meta_title_template' => null,
                'meta_description_template' => null,
                'focus_keyword' => null,
            ],
        ];
    }

    public function getDisplayRules(): array
    {
        return $this->getConfiguration()['display_rules'] ?? [];
    }

    public function getProductRules(): array
    {
        return $this->getConfiguration()['product_rules'] ?? [];
    }

    public function getPricingRules(): array
    {
        return $this->getConfiguration()['pricing_rules'] ?? [];
    }

    public function getInventoryRules(): array
    {
        return $this->getConfiguration()['inventory_rules'] ?? [];
    }

    public function getSeoRules(): array
    {
        return $this->getConfiguration()['seo_rules'] ?? [];
    }
}
