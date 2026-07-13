<?php

/*
 * Copyright (c) 2023 Lloric Mayuga Garcia
 * All rights reserved.
 *
 * 1. Usage Permissions
 *    This software is licensed exclusively to Lloric Mayuga Garcia. The following restrictions apply:
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
 * 📩 For inquiries, contact: lloricode@gmail.com
 * 🌐 Official Website: https://github.com/lloricode
 * 🛒 Purchase Here: https://lloricode.gumroad.com/l/laravel-filament-point-of-sale
 */

declare(strict_types=1);

namespace Domain\Shop\Product\Models;

use Domain\Shop\Brand\Models\Brand;
use Domain\Shop\Category\Models\Category;
use Domain\Shop\Product\Database\Factories\ProductFactory;
use Domain\Shop\Product\Enums\ProductStatus;
use Domain\Shop\Product\Models\EloquentBuilder\ProductEloquentBuilder;
use Domain\Shop\Product\Observers\ProductObserver;
use Domain\Shop\Product\Policies\ProductPolicy;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\UseEloquentBuilder;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
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
use Spatie\Tags\HasTags;

/**
 * @property string $uuid
 * @property string|null $category_uuid
 * @property string|null $brand_uuid
 * @property string $parent_sku
 * @property string $name
 * @property string|null $description
 * @property \Domain\Shop\Product\Enums\ProductStatus $status PHP backed enum
 * @property int $order_column manage by spatie/eloquent-sortable
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Domain\Shop\Product\Models\Attribute> $attributes
 * @property-read \Domain\Shop\Brand\Models\Brand|null $brand
 * @property-read \Domain\Shop\Category\Models\Category|null $category
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property \Illuminate\Database\Eloquent\Collection<int, \Spatie\Tags\Tag> $tags
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Domain\Shop\Product\Models\Sku> $skus
 *
 * @method static \Domain\Shop\Product\Models\EloquentBuilder\ProductEloquentBuilder<static>|\Domain\Shop\Product\Models\Product withAllTags(\ArrayAccess|\Spatie\Tags\Tag|array|string $tags, ?string $type = null)
 * @method static \Domain\Shop\Product\Models\EloquentBuilder\ProductEloquentBuilder<static>|\Domain\Shop\Product\Models\Product withAllTagsOfAnyType($tags)
 * @method static \Domain\Shop\Product\Models\EloquentBuilder\ProductEloquentBuilder<static>|\Domain\Shop\Product\Models\Product withAnyTags(\ArrayAccess|\Spatie\Tags\Tag|array|string $tags, ?string $type = null)
 * @method static \Domain\Shop\Product\Models\EloquentBuilder\ProductEloquentBuilder<static>|\Domain\Shop\Product\Models\Product withAnyTagsOfAnyType($tags)
 * @method static \Domain\Shop\Product\Models\EloquentBuilder\ProductEloquentBuilder<static>|\Domain\Shop\Product\Models\Product withAnyTagsOfType(array|string $type)
 * @method static \Domain\Shop\Product\Models\EloquentBuilder\ProductEloquentBuilder<static>|\Domain\Shop\Product\Models\Product withoutTags(\ArrayAccess|\Spatie\Tags\Tag|array|string $tags, ?string $type = null)
 *
 * @mixin \Eloquent
 */
#[
    UseEloquentBuilder(ProductEloquentBuilder::class),
    UsePolicy(ProductPolicy::class),
    ObservedBy(ProductObserver::class),
    UseFactory(ProductFactory::class)
]
class Product extends Model implements HasMedia, Sortable
{
    /** @use HasBuilder<ProductEloquentBuilder> */
    use HasBuilder;

    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    use HasTags;
    use HasUuids;
    use InteractsWithMedia;
    use LogsActivity;
    use SoftDeletes;
    use SortableTrait;

    #[\Override]
    protected $primaryKey = 'uuid';

    #[\Override]
    protected $fillable = [
        'category_uuid',
        'brand_uuid',
        'parent_sku',
        'name',
        'description',
        'status',
    ];

    #[\Override]
    protected function casts(): array
    {
        return [
            'status' => ProductStatus::class,
        ];
    }

    #[\Override]
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany<\Domain\Shop\Product\Models\Sku, $this> */
    public function skus(): HasMany
    {
        return $this->hasMany(Sku::class);
    }

    #[\Override]
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')
            ->useFallbackUrl(asset('images/no-image.webp'))
            ->registerMediaConversions(function () {
                $this->addMediaConversion('list')
                    ->fit(Fit::Fill, 240, 210);
                $this->addMediaConversion('thumb')
                    ->fit(Fit::Fill, 40, 40);
            });
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\Domain\Shop\Brand\Models\Brand, $this> */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\Domain\Shop\Category\Models\Category, $this> */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany<\Domain\Shop\Product\Models\Attribute, $this> */
    public function attributes(): HasMany
    {
        return $this->hasMany(Attribute::class);
    }
}
