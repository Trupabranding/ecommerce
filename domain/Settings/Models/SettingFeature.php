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

namespace Domain\Settings\Models;

use Domain\Settings\Database\Factories\SettingFeatureFactory;
use Domain\Settings\Observers\SettingFeatureObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property string $uuid
 * @property string $key
 * @property string $name
 * @property string|null $description
 * @property string|null $setting_category_uuid
 * @property bool $enabled
 * @property array|null $default_value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Domain\Settings\Models\SettingCategory|null $category
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Domain\Settings\Models\SettingValue> $values
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 *
 * @mixin \Eloquent
 */
#[
    UseFactory(SettingFeatureFactory::class),
    ObservedBy(SettingFeatureObserver::class),
]
class SettingFeature extends Model
{
    /** @use HasFactory<\Domain\Settings\Database\Factories\SettingFeatureFactory> */
    use HasFactory;

    use HasUuids;
    use LogsActivity;
    use SoftDeletes;

    #[\Override]
    protected $primaryKey = 'uuid';

    #[\Override]
    protected $fillable = [
        'key',
        'name',
        'description',
        'setting_category_uuid',
        'enabled',
        'default_value',
    ];

    #[\Override]
    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
            'default_value' => 'json',
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

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\Domain\Settings\Models\SettingCategory, $this> */
    public function category(): BelongsTo
    {
        return $this->belongsTo(SettingCategory::class, 'setting_category_uuid');
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany<\Domain\Settings\Models\SettingValue, $this> */
    public function values(): HasMany
    {
        return $this->hasMany(SettingValue::class, 'setting_feature_uuid');
    }
}
