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

namespace App\Filament\Admin\Resources\Shop\OrderResource\Pages;

use App\Filament\Admin\Resources\Shop\OrderResource;
use App\Filament\Admin\Resources\Shop\OrderResource\Widgets\TotalOrders;
use App\Filament\Admin\Support\ListRecordsUseFastPaginate;
use Domain\Shop\Order\Enums\OrderStatus;
use Domain\Shop\Order\Exports\OrderExporter;
use Domain\Shop\Order\Models\EloquentBuilder\OrderEloquentBuilder;
use Filament\Actions\CreateAction;
use Filament\Actions\ExportAction;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Override;

class ListOrders extends ListRecords
{
    use ExposesTableToWidgets;
    use ListRecordsUseFastPaginate;

    #[\Override]
    protected static string $resource = OrderResource::class;

    #[Override]
    protected function getHeaderActions(): array
    {
        return [
            ExportAction::make()
                ->translateLabel()
                ->exporter(OrderExporter::class)
                ->authorize('exportAny'),
            //                ->withActivityLog(),
            CreateAction::make()
                ->translateLabel(),
        ];
    }

    #[Override]
    protected function getHeaderWidgets(): array
    {
        return [
            TotalOrders::class,
        ];
    }

    #[Override]
    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All'),
            ...collect(OrderStatus::cases())
                ->mapWithKeys(
                    fn (OrderStatus $status) => [
                        $status->value => Tab::make($status->value)
                            ->query(fn (OrderEloquentBuilder $query) => $query->where('status', $status))
                            ->label($status->getLabel())
                            ->icon($status->getIcon()),
                    ]
                )
                ->toArray(),
        ];
    }
}
