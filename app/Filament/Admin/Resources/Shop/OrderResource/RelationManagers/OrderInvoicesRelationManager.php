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

namespace App\Filament\Admin\Resources\Shop\OrderResource\RelationManagers;

use Domain\Shop\Order\Models\OrderInvoice;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Override;

class OrderInvoicesRelationManager extends RelationManager
{
    #[\Override]
    protected static string $relationship = 'orderInvoices';

    #[Override]
    public function table(Table $table): Table
    {
        return $table
//            ->recordTitleAttribute('file_name')
            ->columns([
                TextColumn::make('file_name')
                    ->translateLabel(),

                TextColumn::make('created_at')
                    ->translateLabel()
                    ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                Action::make('download')
                    ->translateLabel()
                    ->icon(Heroicon::OutlinedPrinter)
                    ->url(
                        fn (OrderInvoice $record) => route('admin.order-invoices.download', $record),
                        shouldOpenInNewTab: true
                    )
                    ->authorize('downloadInvoice'),
            ]);
    }
}
