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

namespace App\Filament\Admin\Widgets;

use App\Filament\Admin\Resources\Shop\OrderResource as MainOrderResourceAlias;
use App\Filament\Admin\Support\TenantHelper;
use App\Filament\Branch\Resources\Shop\OrderResource as BranchOrderResourceAlias;
use Domain\Shop\Order\Models\Order;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Concern\PermissionWidgets;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Contracts\HasPermissionWidgets;
use Override;

class LatestOrders extends TableWidget implements HasPermissionWidgets
{
    use PermissionWidgets;

    #[\Override]
    protected static ?int $sort = 7;

    #[Override]
    public function table(Table $table): Table
    {
        return $table
            ->query(fn () => Order::limit(5)->latest())
            ->columns([
                TextColumn::make('customer.full_name')
                    ->translateLabel()
                    ->placeholder(trans('Guest')),

                TextColumn::make('branch.name')
                    ->hidden(fn (): bool => TenantHelper::getBranch() !== null || ! config()->boolean('app-default.branch_feature_enabled'))
                    ->translateLabel(),

                TextColumn::make('total_price')
                    ->translateLabel()
                    ->money(),

                TextColumn::make('purchased_at')
                    ->translateLabel()
                    ->dateTime(),
            ])
            ->recordActions([
                Action::make('view')
                    ->translateLabel()
                    ->authorize('view')
                    ->url(fn (Order $record): string => match (TenantHelper::getBranch() === null) {
                        true => MainOrderResourceAlias::getUrl('view', ['record' => $record]),
                        default => BranchOrderResourceAlias::getUrl('view', ['record' => $record]),
                    }),
            ])
            ->defaultSort('purchased_at', 'desc')
            ->paginated(false);
    }
}
