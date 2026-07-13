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

namespace Domain\Shop\Stock\Models;

use Domain\Shop\Branch\Models\Branch;
use Domain\Shop\Product\Models\Sku;
use Domain\Shop\Stock\Database\Factories\SkuStockFactory;
use Domain\Shop\Stock\Enums\SkuStockType;
use Domain\Shop\Stock\Models\EloquentBuilder\SkuStockEloquentBuilder;
use Domain\Shop\Stock\Policies\SkuStockPolicy;
use Illuminate\Database\Eloquent\Attributes\UseEloquentBuilder;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\HasBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property string $uuid
 * @property string $branch_uuid
 * @property string $sku_uuid
 * @property \Domain\Shop\Stock\Enums\SkuStockType $type PHP backed enum
 * @property float|null $count when base on stock
 * @property float|null $warning when base on stock
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read \Domain\Shop\Branch\Models\Branch $branch
 * @property-read \Domain\Shop\Product\Models\Sku $sku
 *
 * @mixin \Eloquent
 */
#[
    UseEloquentBuilder(SkuStockEloquentBuilder::class),
    UsePolicy(SkuStockPolicy::class),
    UseFactory(SkuStockFactory::class),
]
class SkuStock extends Model
{
    /** @use HasBuilder<SkuStockEloquentBuilder> */
    use HasBuilder;

    /** @use HasFactory<SkuStockFactory> */
    use HasFactory;

    use HasUuids;
    use LogsActivity;

    #[\Override]
    protected $primaryKey = 'uuid';

    #[\Override]
    protected $fillable = [
        'branch_uuid',
        'type',
        'count',
        'warning',
    ];

    #[\Override]
    protected function casts(): array
    {
        return [
            'count' => 'float',
            'warning' => 'float',
            'type' => SkuStockType::class,
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

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\Domain\Shop\Branch\Models\Branch, $this> */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\Domain\Shop\Product\Models\Sku, $this> */
    public function sku(): BelongsTo
    {
        return $this->belongsTo(Sku::class);
    }

    public function isBaseOnStockWarning(): bool
    {
        return $this->count < $this->warning;
    }
}
