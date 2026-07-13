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

namespace App\Filament\Admin\Widgets;

use Domain\Shop\Order\Enums\OrderPaymentStatus;
use Domain\Shop\Order\Enums\OrderStatus;
use Domain\Shop\Order\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Concern\PermissionWidgets;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Contracts\HasPermissionWidgets;
use Override;

class TotalRevenueStats extends BaseWidget implements HasPermissionWidgets
{
    use PermissionWidgets;

    #[\Override]
    protected static ?int $sort = 2;

    #[Override]
    protected function getStats(): array
    {
        $revenueToday = (float) Order::whereDate('purchased_at', now())
            ->where('payment_status', OrderPaymentStatus::paid)
            ->where('status', OrderStatus::completed)
            ->sum('total_price');

        $revenue7Days = (float) Order::where('purchased_at', '>=', now()->subDays(7)->startOfDay())
            ->where('payment_status', OrderPaymentStatus::paid)
            ->where('status', OrderStatus::completed)
            ->sum('total_price');

        $revenue30Days = (float) Order::where('purchased_at', '>=', now()->subDays(30)->startOfDay())
            ->where('payment_status', OrderPaymentStatus::paid)
            ->where('status', OrderStatus::completed)
            ->sum('total_price');

        return [
            Stat::make(trans('Revenue Today'), moneyFormat($revenueToday / 100)),
            Stat::make(trans('Revenue Last 7 Days'), moneyFormat($revenue7Days / 100)),
            Stat::make(trans('Revenue Last 30 Days'), moneyFormat($revenue30Days / 100)),
        ];
    }
}
