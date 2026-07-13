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

use Domain\Settings\Models\SettingFeature;
use Illuminate\Support\Facades\Cache;

class GetSettingAction
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
        $cacheKey = $this->getCacheKey($featureKey, $branchUuid);
        $branchTag = $branchUuid ? "branch_{$branchUuid}" : 'global';

        return Cache::tags(['settings', 'values', $branchTag])->remember(
            $cacheKey,
            60 * 60, // 1 hour TTL
            function () use ($featureKey, $branchUuid) {
                return $this->resolveValue($featureKey, $branchUuid);
            }
        );
    }

    private function resolveValue(string $featureKey, ?string $branchUuid): mixed
    {
        $feature = SettingFeature::where('key', $featureKey)->first();

        if (!$feature) {
            return null;
        }

        // Check for branch-specific override if branch UUID provided
        if ($branchUuid) {
            $branchValue = $feature->values()
                ->where('branch_uuid', $branchUuid)
                ->first();

            if ($branchValue !== null) {
                return $branchValue->value;
            }
        }

        // Fall back to feature default value
        return $feature->default_value;
    }

    private function getCacheKey(string $featureKey, ?string $branchUuid): string
    {
        $branch = $branchUuid ?? 'global';
        return "setting_{$featureKey}_{$branch}";
    }
}
