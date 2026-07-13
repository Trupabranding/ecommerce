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

namespace Domain\Shop\Order\Pipes\OrderCreated;

use App\Settings\OrderSettings;
use Domain\Access\Admin\Models\Admin;
use Domain\Shop\Order\DataTransferObjects\OrderPipelineData;
use Domain\Shop\Order\Notifications\AdminOrderPlacedNotification;
use Domain\Shop\Order\Notifications\CustomerOrderPlacedNotification;

readonly class NotificationPipe
{
    public function __construct(private OrderSettings $orderSettings) {}

    public function handle(OrderPipelineData $orderPipelineData, callable $next): OrderPipelineData
    {
        $orderPipelineData->order
            ->customer
            ?->notify(new CustomerOrderPlacedNotification($orderPipelineData->order));

        $admins = $orderPipelineData->order
            ->branch
            ->adminNotifications;

        if ($admins->isEmpty()) {
            $admins = $this->orderSettings
                ->getAdminNotifications();
        }

        $admins->each(
            fn (Admin $admin) => $admin
                ->notify(new AdminOrderPlacedNotification($orderPipelineData->order))
        );

        return $next($orderPipelineData);
    }
}
