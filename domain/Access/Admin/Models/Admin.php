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

namespace Domain\Access\Admin\Models;

use Domain\Access\Admin\Database\Factories\AdminFactory;
use Domain\Access\Admin\Models\EloquentBuilder\AdminEloquentBuilder;
use Domain\Access\Admin\Observers\AdminObserver;
use Domain\Access\Admin\Policies\AdminPolicy;
use Domain\Shop\Branch\Models\Branch;
use Domain\Shop\Models\Pivot\AdminBranchOrderNotificationsPivot;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthentication;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthenticationRecovery;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\HasTenants;
use Filament\Notifications\Notification;
use Filament\Panel;
use Filament\Support\Exceptions\Halt;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\UseEloquentBuilder;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\HasBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Concern\PermissionUser;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Config\PermissionConfig;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Contracts\HasPermissionUser;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\FilamentPermissionGenerateName;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\CausesActivity;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;
use Support\Gravatar\GetGravatarAction;

/**
 * @property string $uuid
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string|null $password
 * @property string $timezone
 * @property string|null $theme
 * @property string|null $theme_color
 * @property string|null $remember_token
 * @property string|null $app_authentication_secret
 * @property array<array-key, mixed>|null $app_authentication_recovery_codes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $actions
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read \Domain\Shop\Models\Pivot\AdminBranchOrderNotificationsPivot|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Domain\Shop\Branch\Models\Branch> $branchNotifications
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Domain\Shop\Branch\Models\Branch> $branches
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Domain\Access\Role\Models\Permission> $permissions
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Domain\Access\Role\Models\Role> $roles
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 *
 * @mixin \Eloquent
 */
#[
    UseEloquentBuilder(AdminEloquentBuilder::class),
    UsePolicy(AdminPolicy::class),
    ObservedBy(AdminObserver::class),
    UseFactory(AdminFactory::class),
]
class Admin extends Authenticatable implements FilamentUser, HasAppAuthentication, HasAppAuthenticationRecovery, HasAvatar, HasPermissionUser, HasTenants, MustVerifyEmail
{
    use CausesActivity;
    use HasApiTokens;

    /** @use HasBuilder<AdminEloquentBuilder> */
    use HasBuilder;

    /** @use HasFactory<AdminFactory> */
    use HasFactory;

    use HasRoles;
    use HasUuids;
    use LogsActivity;
    use Notifiable;
    use PermissionUser;
    use SoftDeletes;

    #[\Override]
    protected $primaryKey = 'uuid';

    #[\Override]
    protected $fillable = [
        'name',
        'email',
        'password',
        'timezone',
        'theme',
        'theme_color',
        'app_authentication_secret',
        'app_authentication_recovery_codes',
    ];

    #[\Override]
    protected $hidden = [
        'password',
        'remember_token',
        'app_authentication_secret',
        'app_authentication_recovery_codes',
    ];

    #[\Override]
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'email_verified_at' => 'datetime',
            'app_authentication_secret' => 'encrypted',
            'app_authentication_recovery_codes' => 'encrypted:array',
        ];
    }

    protected function getDefaultGuardName(): string
    {
        // Forcing Use Of A Single Guard
        // https://spatie.be/docs/laravel-permission/basic-usage/multiple-guards#content-forcing-use-of-a-single-guard
        return config()->string('auth.defaults.guard');
    }

    public function isZeroDayAdmin(): bool
    {
        // TODO: isZeroDayAdmin with uuid
        return $this->email === 'ecommerce@mail.test';
    }

    public function canImpersonate(): bool
    {
        return $this->isSuperAdmin();
    }

    public function canBeImpersonated(): bool
    {
        return ! $this->isSuperAdmin();
    }

    public function isBranch(): bool
    {
        if (! config()->boolean('app-default.branch_feature_enabled')) {
            return false;
        }

        if (! array_key_exists('branch', PermissionConfig::roleNamesByGuardName())) {
            return false;
        }

        return $this->hasRole(PermissionConfig::roleName('branch'));
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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<\Domain\Shop\Branch\Models\Branch, $this>
     */
    public function branches(): BelongsToMany
    {
        return $this->belongsToMany(Branch::class);
    }

    #[\Override]
    public function canAccessTenant(Model $tenant): bool
    {
        if (! config()->boolean('app-default.branch_feature_enabled')) {
            return false;
        }

        /** @var \Domain\Shop\Branch\Models\Branch $tenant */
        return $this->can(FilamentPermissionGenerateName::getPanelPermissionName('branch')) && $this->branches->contains($tenant);
    }

    #[\Override]
    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() !== 'branch') {
            return true;
        }

        return $this->isBranchFeatureUnlocked();
    }

    /**
     * @return array<int, \Domain\Shop\Branch\Models\Branch>|\Illuminate\Support\Collection<int, \Domain\Shop\Branch\Models\Branch>
     */
    #[\Override]
    public function getTenants(Panel $panel): array|Collection
    {
        if (! config()->boolean('app-default.branch_feature_enabled')) {
            return collect();
        }

        return $this->branches;
    }

    #[\Override]
    public function getFilamentAvatarUrl(): ?string
    {
        return app(GetGravatarAction::class)->execute($this);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<\Domain\Shop\Branch\Models\Branch, $this>
     */
    public function branchNotifications(): BelongsToMany
    {
        return $this->belongsToMany(
            Branch::class,
            (new AdminBranchOrderNotificationsPivot)->getTable(),
        )
            ->using(AdminBranchOrderNotificationsPivot::class);
    }

    public function getAppAuthenticationSecret(): ?string
    {
        // This method should return the user's saved app authentication secret.

        return $this->app_authentication_secret;
    }

    private function isBranchFeatureUnlocked(): bool
    {
        if (! config()->boolean('app-default.branch_feature_enabled')) {
            return false;
        }

        $licenseHash = config()->string('app-default.branch_feature_license_hash');

        if (blank($licenseHash)) {
            return false;
        }

        $licenseKey = config()->string('app-default.branch_feature_license_key');

        if (blank($licenseKey)) {
            return false;
        }

        return Hash::check($licenseKey, $licenseHash);
    }

    public function saveAppAuthenticationSecret(?string $secret): void
    {
        // This method should save the user's app authentication secret.

        if ($this->email === 'demo.ecommerce@mail.test') {
            Notification::make()
                ->title(trans('Not allowed!'))
                ->body(trans('This is demo account, not allow to set app authentication secret.'))
                ->danger()
                ->send();

            throw (new Halt)->rollBackDatabaseTransaction();
        }

        $this->app_authentication_secret = $secret;
        $this->save();
    }

    public function getAppAuthenticationHolderName(): string
    {
        // In a user's authentication app, each account can be represented by a "holder name".
        // If the user has multiple accounts in your app, it might be a good idea to use
        // their email address as then they are still uniquely identifiable.

        return $this->email;
    }

    /**
     * @return ?array<string>
     */
    public function getAppAuthenticationRecoveryCodes(): ?array
    {
        // This method should return the user's saved app authentication recovery codes.

        return $this->app_authentication_recovery_codes;
    }

    /**
     * @param  array<string> | null  $codes
     */
    public function saveAppAuthenticationRecoveryCodes(?array $codes): void
    {
        // This method should save the user's app authentication recovery codes.

        $this->app_authentication_recovery_codes = $codes;
        $this->save();
    }
}
