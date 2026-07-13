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

namespace App\Filament\Admin\Resources\Shop\CustomerResource\RelationManagers;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Override;

class AddressesRelationManager extends RelationManager
{
    #[\Override]
    protected static string $relationship = 'addresses';

    #[Override]
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('country')
                    ->translateLabel()
                    ->nullable()
                    ->string(),
                TextInput::make('street')
                    ->translateLabel()
                    ->nullable()
                    ->string(),
                TextInput::make('city')
                    ->translateLabel()
                    ->nullable()
                    ->string(),
                TextInput::make('state')
                    ->translateLabel()
                    ->nullable()
                    ->string(),
                TextInput::make('zip')
                    ->translateLabel()
                    ->nullable()
                    ->string(),
            ]);
    }

    #[Override]
    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('country')
                    ->translateLabel()
                    ->wrap()
                    ->sortable()
                    ->searchable(),

                TextColumn::make('street')
                    ->translateLabel()
                    ->wrap()
                    ->sortable()
                    ->searchable(),

                TextColumn::make('city')
                    ->translateLabel()
                    ->wrap()
                    ->sortable()
                    ->searchable(),

                TextColumn::make('state')
                    ->translateLabel()
                    ->wrap()
                    ->sortable()
                    ->searchable(),

                TextColumn::make('zip')
                    ->translateLabel()
                    ->wrap()
                    ->sortable()
                    ->searchable(),

                TextColumn::make('updated_at')
                    ->translateLabel()
                    ->sortable()
                    ->dateTime(),

                TextColumn::make('created_at')
                    ->translateLabel()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable()
                    ->dateTime(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->translateLabel(),
            ])
            ->recordActions([
                EditAction::make()
                    ->translateLabel(),
                ActionGroup::make([
                    DeleteAction::make()
                        ->translateLabel(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->translateLabel(),
                ]),
            ])
            ->emptyStateActions([
                CreateAction::make()
                    ->translateLabel(),
            ]);
    }
}
