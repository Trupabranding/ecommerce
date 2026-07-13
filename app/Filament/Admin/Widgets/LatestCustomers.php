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

use App\Filament\Admin\Resources\Shop\CustomerResource;
use Domain\Shop\Customer\Models\Customer;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Concern\PermissionWidgets;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Contracts\HasPermissionWidgets;
use Override;

class LatestCustomers extends TableWidget implements HasPermissionWidgets
{
    use PermissionWidgets;

    #[\Override]
    protected static ?int $sort = 8;

    #[Override]
    public function table(Table $table): Table
    {
        return $table
            ->query(fn () => Customer::limit(5)->latest())
            ->columns([
                TextColumn::make('full_name')
                    ->translateLabel(),

                TextColumn::make('created_at')
                    ->translateLabel()
                    ->sortable()
                    ->dateTime(),
            ])
            ->recordActions([
                Action::make('view')
                    ->translateLabel()
                    ->authorize('update')
                    ->url(fn (Customer $record): string => CustomerResource::getUrl('edit', ['record' => $record])),
            ])
            ->paginated(false);
    }
}
