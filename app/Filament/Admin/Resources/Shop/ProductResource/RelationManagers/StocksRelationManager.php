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

namespace App\Filament\Admin\Resources\Shop\ProductResource\RelationManagers;

use App\Filament\Admin\Resources\Shop\SkuStockResource;
use Exception;
use Filament\Actions\EditAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Override;

class StocksRelationManager extends RelationManager
{
    #[\Override]
    protected static string $relationship = 'stock';

    #[\Override]
    protected static ?string $recordTitleAttribute = 'id';

    #[Override]
    public function form(Schema $schema): Schema
    {
        return SkuStockResource::form($schema);
    }

    /** @throws Exception */
    #[Override]
    public function table(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('type')
                    ->translateLabel()
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('count')
                    ->translateLabel()
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('warning')
                    ->translateLabel()
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([

            ])
            ->recordActions([
                EditAction::make()
                    ->translateLabel(),

                //                Tables\Actions\DeleteAction::make()
                //                    ->translateLabel(),
            ])
            ->toolbarActions([
                //                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    #[Override]
    public function canCreate(): bool
    {
        return false;
    }

    #[Override]
    public function canDelete(Model $record): bool
    {
        return false;
    }
}
