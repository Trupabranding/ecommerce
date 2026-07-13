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

namespace Domain\Access\Admin\Policies;

use Domain\Access\Admin\Models\Admin;
use Illuminate\Foundation\Auth\User;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Policies\ChecksWildcardPermissions;

class AdminPolicy
{
    use ChecksWildcardPermissions;

    public function before(?User $user, string $ability, mixed $admin = null): ?bool
    {
        if ($admin instanceof Admin && $admin->isZeroDayAdmin()) {
            return false;
        }

        if ($user instanceof Admin && $admin instanceof Admin) {
            if ($admin->isSuperAdmin()) {
                return $user->isSuperAdmin();
            }
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $this->checkWildcardPermissions($user);
    }

    public function create(User $user): bool
    {
        return $this->checkWildcardPermissions($user);
    }

    public function update(User $user, Admin $admin): bool
    {
        return $this->checkWildcardPermissions($user);
    }

    public function delete(User $user, Admin $admin): bool
    {
        return $this->checkWildcardPermissions($user);
    }

    public function restore(User $user, Admin $admin): bool
    {

        return $this->checkWildcardPermissions($user);
    }

    public function forceDelete(User $user, Admin $admin): bool
    {
        return $this->checkWildcardPermissions($user);
    }

    public function updatePassword(User $user, Admin $admin): bool
    {
        if ($admin->trashed()) {
            return false;
        }

        return $this->checkWildcardPermissions($user);
    }

    public function resendEmailVerification(User $user, Admin $admin): bool
    {
        if ($admin->trashed()) {
            return false;
        }

        return $this->checkWildcardPermissions($user);
    }
}
