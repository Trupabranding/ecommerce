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

namespace Domain\Access\Admin\Observers;

use App\Observers\LogAttemptDeleteResource;
use App\Settings\OrderSettings;
use Domain\Access\Admin\Models\Admin;
use Filament\Support\Exceptions\Halt;

class AdminObserver
{
    use LogAttemptDeleteResource;

    public function __construct(private readonly OrderSettings $orderSettings) {}

    /**
     * @throws Halt
     */
    public function deleting(Admin $admin): void
    {
        if (in_array($admin->getKey(), $this->orderSettings->admin_notification_ids, true)) {

            self::abortThenLogAttemptDelete(
                $admin,
                trans('Can not delete admin with associated orders settings.'),
                [
                    'admin_notification_ids' => implode(', ', $this->orderSettings->admin_notification_ids),
                ]
            );

        }
    }
}
