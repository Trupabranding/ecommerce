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

namespace Domain\Settings\Models;

use Domain\Settings\Database\Factories\SettingCategoryFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $uuid
 * @property string $slug
 * @property string $name
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Domain\Settings\Models\AdminSetting> $adminSettings
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Domain\Settings\Models\SettingFeature> $settingFeatures
 *
 * @mixin \Eloquent
 */
#[UseFactory(SettingCategoryFactory::class)]
class SettingCategory extends Model
{
    /** @use HasFactory<\Domain\Settings\Database\Factories\SettingCategoryFactory> */
    use HasFactory;

    use HasUuids;

    #[\Override]
    protected $primaryKey = 'uuid';

    #[\Override]
    protected $fillable = [
        'slug',
        'name',
        'description',
    ];

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany<\Domain\Settings\Models\AdminSetting, $this> */
    public function adminSettings(): HasMany
    {
        return $this->hasMany(AdminSetting::class, 'setting_category_uuid');
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany<\Domain\Settings\Models\SettingFeature, $this> */
    public function settingFeatures(): HasMany
    {
        return $this->hasMany(SettingFeature::class, 'setting_category_uuid');
    }
}
