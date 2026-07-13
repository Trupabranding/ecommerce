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
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Concern\PermissionWidgets;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Contracts\HasPermissionWidgets;
use Override;

class IncomePerDayChart extends ChartWidget implements HasPermissionWidgets
{
    use InteractsWithPageFilters;
    use PermissionWidgets;

    #[\Override]
    protected static ?int $sort = 6;

    #[Override]
    public function getHeading(): ?string
    {
        return trans('Income Per Day');
    }

    #[Override]
    protected function getData(): array
    {
        $data = Trend::query(
            Order::where('payment_status', OrderPaymentStatus::paid)
                ->where('status', OrderStatus::completed)
        )
            ->dateColumn('purchased_at')
            ->between(
                start: $this->getDateFilter('start_date') ?? now()->subMonth(),
                end: $this->getDateFilter('end_date') ?? now(),
            )
            ->perDay()
            ->sum('total_price');

        return [
            'datasets' => [
                [
                    'label' => trans('Income Per Day'),
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate / 100),
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => $value->date),
        ];
    }

    #[Override]
    protected function getType(): string
    {
        return 'line';
    }
}
