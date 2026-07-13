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

namespace Domain\Settings\Policies;

use Domain\Admin\Models\Admin;
use Domain\Settings\Models\SettingFeature;

/**
 * Policy for controlling access to settings features
 *
 * Permission hierarchy:
 * - Super Admin: Full access
 * - Branch Manager: Branch-scoped settings only
 * - Operator: View-only access
 */
class SettingsPolicy
{
    /**
     * Admin can view any settings
     *
     * @param Admin $admin
     * @return bool
     */
    public function viewAny(Admin $admin): bool
    {
        return $admin->can('settings.view');
    }

    /**
     * Admin can view a specific setting
     *
     * @param Admin $admin
     * @param SettingFeature $feature
     * @return bool
     */
    public function view(Admin $admin, SettingFeature $feature): bool
    {
        // Check general view permission
        if (!$admin->can('settings.view')) {
            return false;
        }

        // Check feature-specific permission
        return $admin->can("settings.feature.{$feature->key}");
    }

    /**
     * Admin can create a new setting feature
     * Only Super Admin can create
     *
     * @param Admin $admin
     * @return bool
     */
    public function create(Admin $admin): bool
    {
        return $admin->can('settings.create') && $admin->isSuperAdmin();
    }

    /**
     * Admin can update a setting feature
     * Hierarchy:
     * - Super Admin: All features
     * - Branch Manager: Branch-scoped overrides only
     * - Operator: No update access
     *
     * @param Admin $admin
     * @param SettingFeature $feature
     * @return bool
     */
    public function update(Admin $admin, SettingFeature $feature): bool
    {
        // Only super admin can edit feature definitions
        if (!$admin->can('settings.edit')) {
            return false;
        }

        return $admin->isSuperAdmin() || $admin->can("settings.feature.{$feature->key}");
    }

    /**
     * Admin can delete a setting feature
     * Only Super Admin can delete
     *
     * @param Admin $admin
     * @param SettingFeature $feature
     * @return bool
     */
    public function delete(Admin $admin, SettingFeature $feature): bool
    {
        return $admin->can('settings.delete') && $admin->isSuperAdmin();
    }

    /**
     * Admin can customize a setting
     * Hierarchy:
     * - Super Admin: All customizations
     * - Branch Manager: Branch-specific customizations
     * - Operator: No customization access
     *
     * @param Admin $admin
     * @param SettingFeature $feature
     * @return bool
     */
    public function customize(Admin $admin, SettingFeature $feature): bool
    {
        return $admin->can('settings.customize') && $admin->can("settings.feature.{$feature->key}");
    }

    /**
     * Admin can override settings for a branch
     * Only Branch Manager and Super Admin
     *
     * @param Admin $admin
     * @return bool
     */
    public function overrideBranch(Admin $admin): bool
    {
        return $admin->can('settings.override.branch') &&
               ($admin->isSuperAdmin() || $admin->isManager());
    }

    /**
     * Check if admin has view-only access
     *
     * @param Admin $admin
     * @return bool
     */
    public function viewOnly(Admin $admin): bool
    {
        return !$admin->can('settings.edit') && $admin->can('settings.view');
    }
}
