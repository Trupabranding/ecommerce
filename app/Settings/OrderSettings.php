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

namespace App\Settings;

use Domain\Access\Admin\Models\Admin;
use Illuminate\Database\Eloquent\Collection;
use Override;

class OrderSettings extends BaseSettings
{
    public string $prefix;

    /**
     * @var array<int, int>
     */
    public array $admin_notification_ids;

    public int $maximum_advance_booking_days;

    public int $auto_cancel_unpaid_minutes;

    public bool $allow_guest_checkout;

    /**
     * @var array<int, string>
     */
    public array $allowed_payment_methods;

    public ?string $default_payment_method = null;

    public int $daily_order_limit;

    #[Override]
    public static function group(): string
    {
        return 'order';
    }

    /**
     * @return Collection<int, Admin>
     */
    public function getAdminNotifications(): Collection
    {
        return Admin::whereKey($this->admin_notification_ids)->get();
    }
}
