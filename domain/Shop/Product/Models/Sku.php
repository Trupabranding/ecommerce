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

namespace Domain\Shop\Product\Models;

use App\Casts\MoneyCast;
use Domain\Shop\Cart\Models\Cart;
use Domain\Shop\Product\Database\Factories\SkuFactory;
use Domain\Shop\Product\Enums\SkuMinimumType;
use Domain\Shop\Product\Observers\SkuObserver;
use Domain\Shop\Stock\Models\SkuStock;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * @property string $uuid
 * @property string $product_uuid
 * @property string $code
 * @property float $price for money
 * @property float|null $minimum
 * @property float|null $maximum
 * @property \Domain\Shop\Product\Enums\SkuMinimumType|null $minimum_type PHP backed enum
 * @property int $order_column manage by spatie/eloquent-sortable
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Domain\Shop\Product\Models\AttributeOption> $attributeOptions
 * @property-read array $attribute_options_list
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Domain\Shop\Cart\Models\Cart> $carts
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read \Domain\Shop\Product\Models\Product $product
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Domain\Shop\Stock\Models\SkuStock> $skuStocks
 *
 * @mixin \Eloquent
 */
#[
    ObservedBy(SkuObserver::class),
    UseFactory(SkuFactory::class)
]
class Sku extends Model implements HasMedia, Sortable
{
    /** @use HasFactory<SkuFactory> */
    use HasFactory;

    use HasUuids;
    use InteractsWithMedia;
    use LogsActivity;
    use SortableTrait;

    #[\Override]
    protected $primaryKey = 'uuid';

    #[\Override]
    protected $fillable = [
        'code',
        'price',
        'minimum',
        'maximum',
        'order_column',
        'minimum_type',
    ];

    #[\Override]
    protected function casts(): array
    {
        return [
            'price' => MoneyCast::class,
            'minimum_type' => SkuMinimumType::class,
        ];
    }

    /** @return Attribute<array, never> */
    protected function attributeOptionsList(): Attribute
    {
        return Attribute::get(
            fn (): array => $this->attributeOptions
                ->map(
                    fn (AttributeOption $attributeOption) => $attributeOption->label
                )
                ->toArray()
        );
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\Domain\Shop\Product\Models\Product, $this> */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<\Domain\Shop\Product\Models\AttributeOption, $this> */
    public function attributeOptions(): BelongsToMany
    {
        return $this->belongsToMany(AttributeOption::class);
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

    #[\Override]
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany<\Domain\Shop\Stock\Models\SkuStock, $this> */
    public function skuStocks(): HasMany
    {
        return $this->hasMany(SkuStock::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany<\Domain\Shop\Cart\Models\Cart, $this> */
    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class);
    }
}
