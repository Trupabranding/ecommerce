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

namespace Domain\Shop\Branch\Models;

use Domain\Access\Admin\Models\Admin;
use Domain\Shop\Branch\Database\Factories\BranchFactory;
use Domain\Shop\Branch\Enums\BranchStatus;
use Domain\Shop\Branch\Models\EloquentBuilder\BranchEloquentBuilder;
use Domain\Shop\Branch\Observers\BranchObserver;
use Domain\Shop\Branch\Policies\BranchPolicy;
use Domain\Shop\Cart\Models\Cart;
use Domain\Shop\Models\Pivot\AdminBranchOrderNotificationsPivot;
use Domain\Shop\OperationHour\Actions\GetOperationHoursHumanReadableByBranchAction;
use Domain\Shop\OperationHour\Enums\OperationHourType;
use Domain\Shop\OperationHour\Models\OperationHour;
use Domain\Shop\Order\Models\Order;
use Domain\Shop\Stock\Models\SkuStock;
use Filament\Models\Contracts\HasAvatar;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\UseEloquentBuilder;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\HasBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
 * @property string $code
 * @property string $name
 * @property string|null $address
 * @property string|null $phone
 * @property string|null $email
 * @property string|null $website
 * @property \Domain\Shop\Branch\Enums\BranchStatus $status PHP backed enum
 * @property int|null $maximum_advance_booking_days
 * @property int $order_column manage by spatie/eloquent-sortable
 * @property bool $is_operation_hours_enabled
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read \Domain\Shop\Models\Pivot\AdminBranchOrderNotificationsPivot|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Domain\Access\Admin\Models\Admin> $adminNotifications
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Domain\Access\Admin\Models\Admin> $admins
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Domain\Shop\Cart\Models\Cart> $carts
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Domain\Shop\OperationHour\Models\OperationHour> $operationHours
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Domain\Shop\OperationHour\Models\OperationHour> $operationHoursInStore
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Domain\Shop\OperationHour\Models\OperationHour> $operationHoursOnline
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Domain\Shop\Order\Models\Order> $orders
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Domain\Shop\Stock\Models\SkuStock> $skuStocks
 *
 * @mixin \Eloquent
 */
#[
    UseEloquentBuilder(BranchEloquentBuilder::class),
    UsePolicy(BranchPolicy::class),
    ObservedBy(BranchObserver::class),
    UseFactory(BranchFactory::class)
]
class Branch extends Model implements HasAvatar, HasMedia, Sortable
{
    /** @use HasBuilder<BranchEloquentBuilder> */
    use HasBuilder;

    /** @use HasFactory<BranchFactory> */
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
        'code',
        'name',
        'address',
        'phone',
        'email',
        'website',
        'status',
        'is_operation_hours_enabled',
        'maximum_advance_booking_days',
    ];

    #[\Override]
    protected function casts(): array
    {
        return [
            'status' => BranchStatus::class,
            'is_operation_hours_enabled' => 'bool',
            'maximum_advance_booking_days' => 'int',
        ];
    }

    #[\Override]
    public function getRouteKeyName(): string
    {
        return 'code';
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
            ->useFallbackUrl(asset('images/no-image.webp'))
            ->registerMediaConversions(function () {
                $this->addMediaConversion('list')
                    ->fit(Fit::Fill, 240, 210);
                $this->addMediaConversion('thumb')
                    ->fit(Fit::Fill, 40, 40);
            });

        $this->addMediaCollection('panel')
            ->singleFile()
            ->useFallbackUrl(asset('images/no-image.webp'))
            ->registerMediaConversions(function () {
                $this->addMediaConversion('thumb')
                    ->fit(Fit::Fill, 40, 40);
            });
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany<\Domain\Shop\Order\Models\Order, $this> */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany<\Domain\Shop\Stock\Models\SkuStock, $this> */
    public function skuStocks(): HasMany
    {
        return $this->hasMany(SkuStock::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\Domain\Shop\OperationHour\Models\OperationHour, $this>
     */
    public function operationHours(): HasMany
    {
        return $this->hasMany(OperationHour::class);
    }

    /**
     * @return HasMany<\Domain\Shop\OperationHour\Models\OperationHour>
     */
    public function operationHoursOnline(): HasMany
    {
        return $this->operationHours()
            ->where('type', OperationHourType::online);
    }

    /**
     * @return HasMany<\Domain\Shop\OperationHour\Models\OperationHour>
     */
    public function operationHoursInStore(): HasMany
    {
        return $this->operationHours()
            ->where('type', OperationHourType::in_store);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<\Domain\Access\Admin\Models\Admin, $this>
     */
    public function admins(): BelongsToMany
    {
        return $this->belongsToMany(Admin::class);
    }

    #[\Override]
    public function getFilamentAvatarUrl(): ?string
    {
        return $this->getFirstMediaUrl('panel', 'thumb');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<\Domain\Access\Admin\Models\Admin, $this>
     */
    public function adminNotifications(): BelongsToMany
    {
        return $this->belongsToMany(
            Admin::class,
            (new AdminBranchOrderNotificationsPivot)->getTable(),
        )
            ->using(AdminBranchOrderNotificationsPivot::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\Domain\Shop\Cart\Models\Cart, $this>
     */
    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class);
    }

    /**
     * @return array<int, string>
     */
    public function operationHoursHumanReadable(?OperationHourType $type = null): array
    {
        return app(GetOperationHoursHumanReadableByBranchAction::class)->execute($this, $type);
    }
}
