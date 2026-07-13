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

namespace App\Filament\Admin\Resources\Shop\ProductResource\Pages;

use App\Filament\Admin\Resources\Shop\ProductResource;
use App\Filament\Admin\Resources\Shop\ProductResource\Widgets\ProductStats;
use App\Filament\Admin\Support\ListRecordsUseFastPaginate;
use Domain\Shop\Product\Enums\ProductStatus;
use Domain\Shop\Product\Models\EloquentBuilder\ProductEloquentBuilder;
use Filament\Actions\CreateAction;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Override;

class ListProducts extends ListRecords
{
    use ExposesTableToWidgets;
    use ListRecordsUseFastPaginate;

    #[\Override]
    protected static string $resource = ProductResource::class;

    #[Override]
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->translateLabel(),
        ];
    }

    #[Override]
    protected function getHeaderWidgets(): array
    {
        return [
            ProductStats::class,
        ];
    }

    #[Override]
    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All'),
            ...collect(ProductStatus::cases())
                ->mapWithKeys(
                    fn (ProductStatus $status) => [
                        $status->value => Tab::make($status->value)
                            ->query(fn (ProductEloquentBuilder $query) => $query->where('status', $status))
                            ->label($status->getLabel())
                            ->icon($status->getIcon()),
                    ]
                )
                ->toArray(),
        ];
    }
}
