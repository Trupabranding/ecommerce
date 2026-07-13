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

namespace Domain\Shop\OperationHour\Models;

use Domain\Shop\Branch\Models\Branch;
use Domain\Shop\OperationHour\Database\Factories\OperationHourFactory;
use Domain\Shop\OperationHour\Enums\OperationHourDay;
use Domain\Shop\OperationHour\Enums\OperationHourType;
use Domain\Shop\OperationHour\Policies\OperationHourPolicy;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

/**
 * @property string $uuid
 * @property string $branch_uuid
 * @property \Domain\Shop\OperationHour\Enums\OperationHourDay $day PHP backed enum
 * @property \Domain\Shop\OperationHour\Enums\OperationHourType $type PHP backed enum
 * @property bool $is_all_day
 * @property bool $is_open
 * @property int $order_column manage by spatie/eloquent-sortable
 * @property \Illuminate\Support\Carbon $from
 * @property \Illuminate\Support\Carbon $to
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read \Domain\Shop\Branch\Models\Branch $branch
 *
 * @mixin \Eloquent
 */
#[
    UsePolicy(OperationHourPolicy::class),
    UseFactory(OperationHourFactory::class)
]
class OperationHour extends Model implements Sortable
{
    /** @use HasFactory<OperationHourFactory> */
    use HasFactory;

    use HasUuids;
    use LogsActivity;
    use SortableTrait;

    #[\Override]
    protected $primaryKey = 'uuid';

    #[\Override]
    protected $fillable = [
        'day',
        'from',
        'to',
        'is_all_day',
        'is_open',
        'type',
        'order_column',
    ];

    #[\Override]
    protected function casts(): array
    {
        return [
            'from' => 'datetime',
            'to' => 'datetime',
            'day' => OperationHourDay::class,
            'type' => OperationHourType::class,
            'is_all_day' => 'bool',
            'is_open' => 'bool',
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

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\Domain\Shop\Branch\Models\Branch, $this>
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
