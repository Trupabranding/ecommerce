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

namespace App\Providers;

use App\Policies\ActivityPolicy;
use Domain\Access\Admin\Models\Admin;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\FilamentPermissionGenerateName;
use Spatie\Activitylog\Models\Activity;

class AuthServiceProvider extends ServiceProvider
{
    /** @var array<class-string, class-string> */
    #[\Override]
    protected $policies = [
        Activity::class => ActivityPolicy::class,
    ];

    public function boot(): void
    {
        self::defineGates();
    }

    private static function defineGates(): void
    {
        $checkPermission = fn (string $permissionsName) => fn (?Authenticatable $user): bool => $user instanceof Admin && $user
            ->can(
                FilamentPermissionGenerateName::getCustomPermissionName($permissionsName)
            );

        Gate::define('viewLogViewer', $checkPermission('viewLogViewer'));
        Gate::define('viewPulse', $checkPermission('viewPulse'));
        Gate::define('download-backup', $checkPermission('downloadBackup'));
        Gate::define('delete-backup', $checkPermission('deleteBackup'));
        //        Gate::define('viewApiDocs', fn (Admin $user) => true); // allow public access
    }
}
