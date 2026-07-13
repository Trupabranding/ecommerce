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

namespace Domain\Shop\Cart\Models;

use App\Casts\MoneyCast;
use Domain\Shop\Branch\Models\Branch;
use Domain\Shop\Cart\Database\Factories\CartFactory;
use Domain\Shop\Cart\Policies\CartPolicy;
use Domain\Shop\Customer\Models\Customer;
use Domain\Shop\Product\Models\Product;
use Domain\Shop\Product\Models\Sku;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property string $uuid
 * @property string $customer_uuid
 * @property string $branch_uuid
 * @property string $product_uuid
 * @property string $sku_uuid
 * @property string $sku_code
 * @property string $product_name
 * @property float $price for money
 * @property float $quantity
 * @property float|null $minimum
 * @property float|null $maximum
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read \Domain\Shop\Branch\Models\Branch $branch
 * @property-read \Domain\Shop\Customer\Models\Customer $customer
 * @property-read \Domain\Shop\Product\Models\Product $product
 * @property-read \Domain\Shop\Product\Models\Sku $sku
 *
 * @mixin \Eloquent
 */
#[
    UsePolicy(CartPolicy::class),
    UseFactory(CartFactory::class),
]
class Cart extends Model
{
    /** * @use HasFactory<CartFactory>     */
    use HasFactory;

    use HasUuids;
    use LogsActivity;

    #[\Override]
    protected $primaryKey = 'uuid';

    #[\Override]
    protected $fillable = [
        'customer_uuid',
        'branch_uuid',
        'product_uuid',
        'sku_uuid',
        'product_name',
        'sku_code',
        'price',
        'quantity',
        'minimum',
        'maximum',
    ];

    #[\Override]
    protected function casts(): array
    {
        return [
            'quantity' => 'float',
            'price' => MoneyCast::class,
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

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\Domain\Shop\Customer\Models\Customer, $this> */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\Domain\Shop\Product\Models\Sku, $this> */
    public function sku(): BelongsTo
    {
        return $this->belongsTo(Sku::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\Domain\Shop\Branch\Models\Branch, $this> */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\Domain\Shop\Product\Models\Product, $this> */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
