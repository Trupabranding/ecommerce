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

use Brick\Money\Money;
use Domain\Access\Admin\Models\Admin;
use Domain\Shop\Customer\Models\Customer;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Number;

if (! function_exists('filament_admin')) {

    function filament_admin(): Admin
    {
        /** @phpstan-ignore return.type */
        return once(fn () => Filament::auth()->user());
    }
}

if (! function_exists('customer_auth')) {

    function customer_auth(): Customer
    {
        /** @phpstan-ignore return.type */
        return Auth::user();
    }
}

if (! function_exists('money')) {
    function money(float|string $amount): Money
    {
        return Money::of(
            $amount,
            config()->string('app-default.currency'),
        );
    }
}

if (! function_exists('moneyFormat')) {
    function moneyFormat(float|int $amount): string
    {
        return (string) Number::currency($amount);
    }
}

if (! function_exists('moneyAmountToFloat')) {
    function moneyAmountToFloat(Money $money): float
    {
        return (float) ((string) $money->getAmount());
    }
}
