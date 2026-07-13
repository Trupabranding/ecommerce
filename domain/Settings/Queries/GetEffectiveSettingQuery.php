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

namespace Domain\Settings\Queries;

use Domain\Settings\Models\SettingFeature;
use Domain\Settings\Models\SettingValue;

/**
 * Query for resolving effective setting values with inheritance.
 *
 * Implements cascading resolution:
 * 1. If branch_uuid provided AND branch override exists -> return branch_value
 * 2. Else if global setting exists -> return global_value
 * 3. Else -> return feature_default_value
 */
class GetEffectiveSettingQuery
{
    /**
     * Get the effective value for a setting feature with optional branch context
     *
     * @param string $featureKey The key of the setting feature
     * @param string|null $branchUuid The branch UUID for branch-scoped settings
     * @return mixed The setting value or null
     */
    public function execute(string $featureKey, ?string $branchUuid = null): mixed
    {
        $feature = SettingFeature::where('key', $featureKey)->first();

        if (!$feature) {
            return null;
        }

        // Check for branch-specific override if branch UUID provided
        if ($branchUuid) {
            $branchValue = $this->getBranchValue($feature, $branchUuid);
            if ($branchValue !== null) {
                return $branchValue->value;
            }
        }

        // Fall back to feature default value
        return $feature->default_value;
    }

    /**
     * Get branch-specific override value
     *
     * @param SettingFeature $feature
     * @param string $branchUuid
     * @return SettingValue|null
     */
    private function getBranchValue(SettingFeature $feature, string $branchUuid): ?SettingValue
    {
        return $feature->values()
            ->where('branch_uuid', $branchUuid)
            ->first();
    }

    /**
     * Check if a branch has an override for a feature
     *
     * @param string $featureKey
     * @param string $branchUuid
     * @return bool
     */
    public function hasBranchOverride(string $featureKey, string $branchUuid): bool
    {
        return SettingValue::whereHas('feature', fn ($q) => $q->where('key', $featureKey))
            ->where('branch_uuid', $branchUuid)
            ->exists();
    }
}
