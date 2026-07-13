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

namespace Domain\Shop\Order\Models;

use App\Casts\MoneyCast;
use Domain\Shop\Branch\Models\Branch;
use Domain\Shop\Customer\Models\Customer;
use Domain\Shop\Order\Database\Factories\OrderFactory;
use Domain\Shop\Order\Enums\ClaimType;
use Domain\Shop\Order\Enums\OrderPaymentMethod;
use Domain\Shop\Order\Enums\OrderPaymentStatus;
use Domain\Shop\Order\Enums\OrderStatus;
use Domain\Shop\Order\Models\EloquentBuilder\OrderEloquentBuilder;
use Domain\Shop\Order\Observers\OrderObserver;
use Domain\Shop\Order\Policies\OrderPolicy;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
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
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * @property string $uuid
 * @property string $branch_uuid
 * @property string|null $customer_uuid
 * @property string $receipt_number
 * @property float $delivery_price for money
 * @property float $total_price for money
 * @property string|null $notes
 * @property \Domain\Shop\Order\Enums\OrderPaymentMethod|null $payment_method PHP backed enum
 * @property \Domain\Shop\Order\Enums\OrderPaymentStatus $payment_status PHP backed enum
 * @property \Domain\Shop\Order\Enums\OrderStatus $status PHP backed enum
 * @property \Domain\Shop\Order\Enums\ClaimType $claim_type PHP backed enum
 * @property \Illuminate\Support\Carbon|null $claim_at
 * @property \Illuminate\Support\Carbon $purchased_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read \Domain\Shop\Branch\Models\Branch $branch
 * @property-read \Domain\Shop\Customer\Models\Customer|null $customer
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Domain\Shop\Order\Models\OrderInvoice> $orderInvoices
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Domain\Shop\Order\Models\OrderItem> $orderItems
 *
 * @mixin \Eloquent
 */
#[
    UsePolicy(OrderPolicy::class),
    ObservedBy(OrderObserver::class),
    UseFactory(OrderFactory::class)
]
class Order extends Model implements HasMedia
{
    /** @use HasBuilder<OrderEloquentBuilder> */
    use HasBuilder;

    /** @use HasFactory<OrderFactory> */
    use HasFactory;

    use HasUuids;
    use InteractsWithMedia;
    use LogsActivity;
    use SoftDeletes;

    #[\Override]
    protected $primaryKey = 'uuid';

    #[\Override]
    protected static string $builder = OrderEloquentBuilder::class;

    #[\Override]
    protected $fillable = [
        'branch_uuid',
        'customer_uuid',
        'receipt_number',
        'notes',
        'delivery_price',
        'total_price',
        'payment_method',
        'payment_status',
        'status',
        'claim_type',
        'claim_at',
        'purchased_at',
    ];

    #[\Override]
    protected function casts(): array
    {
        return [
            'payment_method' => OrderPaymentMethod::class,
            'payment_status' => OrderPaymentStatus::class,
            'status' => OrderStatus::class,
            'delivery_price' => MoneyCast::class,
            'total_price' => MoneyCast::class,
            'claim_type' => ClaimType::class,
            'claim_at' => 'datetime',
            'purchased_at' => 'datetime',
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

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany<\Domain\Shop\Order\Models\OrderItem, $this> */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\Domain\Shop\Customer\Models\Customer, $this> */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\Domain\Shop\Branch\Models\Branch, $this> */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany<\Domain\Shop\Order\Models\OrderInvoice, $this> */
    public function orderInvoices(): HasMany
    {
        return $this->hasMany(OrderInvoice::class)->latest();
    }
    //    public function registerMediaCollections(): void
    //    {
    //        $this->addMediaCollection('invoice')
    //            ->acceptsFile(fn () => ['application/pdf'])
    //            ->singleFile();
    //    }

}
