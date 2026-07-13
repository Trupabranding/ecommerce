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

namespace Domain\Settings\Observers;

use Domain\Settings\Models\SettingValue;
use Illuminate\Support\Facades\Cache;

class SettingValueObserver
{
    public function created(SettingValue $value): void
    {
        $this->invalidateCache($value);
    }

    public function updated(SettingValue $value): void
    {
        $this->invalidateCache($value);
    }

    public function deleted(SettingValue $value): void
    {
        $this->invalidateCache($value);
    }

    private function invalidateCache(SettingValue $value): void
    {
        $branchUuid = $value->branch_uuid ?? 'global';
        Cache::tags(['settings', 'values', "branch_{$branchUuid}"])->flush();
    }
}
