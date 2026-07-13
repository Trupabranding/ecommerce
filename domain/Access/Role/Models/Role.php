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

namespace Domain\Access\Role\Models;

use Domain\Access\Role\Models\EloquentBuilder\RoleEloquentBuilder;
use Domain\Access\Role\Observers\RoleObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\UseEloquentBuilder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\HasBuilder;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Config\PermissionConfig;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property string $uuid
 * @property string $name
 * @property string $guard_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Domain\Access\Role\Models\Permission> $permissions
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Domain\Access\Admin\Models\Admin> $users
 *
 * @mixin \Eloquent
 */
#[
    UseEloquentBuilder(RoleEloquentBuilder::class),
    ObservedBy(RoleObserver::class),
]
class Role extends \Spatie\Permission\Models\Role
{
    /** @use HasBuilder<RoleEloquentBuilder> */
    use HasBuilder;

    use HasUuids;
    use LogsActivity;

    #[\Override]
    protected $primaryKey = 'uuid';

    #[\Override]
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logUnguarded()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public static function superAdmin(): self
    {
        return self::findByNameOnceCached(PermissionConfig::superAdmin());
    }

    public static function admin(): self
    {
        return self::findByNameOnceCached(PermissionConfig::admin());
    }

    public static function findByNameOnceCached(string $name, ?string $guard = null): self
    {
        return once(function () use ($name, $guard) {
            /** @var self $role */
            $role = static::findByName($name, $guard ?? PermissionConfig::defaultGuardName());

            return $role;
        });
    }
}
