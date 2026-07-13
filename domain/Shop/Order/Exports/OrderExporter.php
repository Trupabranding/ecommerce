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

namespace Domain\Shop\Order\Exports;

use App\Jobs\QueueName;
use Domain\Shop\Order\Models\Order;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Str;

class OrderExporter extends Exporter
{
    #[\Override]
    protected static ?string $model = Order::class;

    #[\Override]
    public function getJobQueue(): ?string
    {
        return QueueName::EXPORTS->value;
    }

    #[\Override]
    public static function getColumns(): array
    {
        return array_values(array_filter([
            ExportColumn::make('receipt_number'),

            config()->boolean('app-default.branch_feature_enabled')
                ? ExportColumn::make('branch.name')
                : null,

            ExportColumn::make('customer')
                ->state(
                    fn (Order $record): string => $record->customer->full_name ?? trans('Guest')
                ),

            ExportColumn::make('total_price')
                ->state(
                    fn (Order $record): string => moneyFormat($record->total_price)
                ),

            ExportColumn::make('payment_method')
                ->state(fn (Order $record) => $record->payment_method?->getLabel() ?? '--'),

            ExportColumn::make('payment_status')
                ->state(fn (Order $record) => $record->payment_status->getLabel()),

            ExportColumn::make('status')
                ->state(fn (Order $record) => $record->status->getLabel()),

            ExportColumn::make('created_at')
                ->state(
                    fn (Order $record) => $record->created_at
                        // TODO: timezone on export
//                                            ?->setTimezone(
//                                                filament_admin()->timezone
//                                            )
                        ?->format(config()->string('app-default.date_time_display_format'))
                ),
            ]));
    }

    #[\Override]
    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your order export has completed and '.number_format($export->successful_rows).
            ' '.Str::of('row')->plural($export->successful_rows).' exported.';

        if (($failedRowsCount = $export->getFailedRowsCount()) > 0) {
            $body .= ' '.number_format($failedRowsCount).
                ' '.Str::of('row')->plural($failedRowsCount).' failed to export.';
        }

        return $body;
    }
}
