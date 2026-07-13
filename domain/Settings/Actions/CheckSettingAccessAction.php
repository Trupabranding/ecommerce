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

namespace Domain\Settings\Actions;

use Domain\Admin\Models\Admin;
use Domain\Settings\Models\SettingFeature;

/**
 * Action to check if a user has access to a specific setting feature
 */
class CheckSettingAccessAction
{
    /**
     * Check if admin can view a setting feature
     *
     * @param Admin $admin
     * @param SettingFeature $feature
     * @return bool
     */
    public function canView(Admin $admin, SettingFeature $feature): bool
    {
        return $admin->can('view', $feature);
    }

    /**
     * Check if admin can edit a setting feature
     *
     * @param Admin $admin
     * @param SettingFeature $feature
     * @return bool
     */
    public function canEdit(Admin $admin, SettingFeature $feature): bool
    {
        return $admin->can('update', $feature);
    }

    /**
     * Check if admin can customize a setting feature
     *
     * @param Admin $admin
     * @param SettingFeature $feature
     * @return bool
     */
    public function canCustomize(Admin $admin, SettingFeature $feature): bool
    {
        return $admin->can('customize', $feature);
    }

    /**
     * Check if admin can delete a setting feature
     *
     * @param Admin $admin
     * @param SettingFeature $feature
     * @return bool
     */
    public function canDelete(Admin $admin, SettingFeature $feature): bool
    {
        return $admin->can('delete', $feature);
    }

    /**
     * Check if admin can override settings for a branch
     *
     * @param Admin $admin
     * @return bool
     */
    public function canOverrideBranch(Admin $admin): bool
    {
        return $admin->can('overrideBranch', SettingFeature::class);
    }

    /**
     * Check if admin has only view access (no edit)
     *
     * @param Admin $admin
     * @return bool
     */
    public function isViewOnly(Admin $admin): bool
    {
        return $admin->can('viewOnly', SettingFeature::class);
    }
}
