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

namespace App\Filament\Admin\Resources\Shop\ProductResource\RelationManagers;

use Domain\Shop\Product\Enums\AttributeFieldType;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\Rules\Unique;
use Override;

/**
 * @property-read \Domain\Shop\Product\Models\Product $ownerRecord
 */
class AttributesRelationManager extends RelationManager
{
    #[\Override]
    protected static string $relationship = 'attributes';

    public static function getBadge(Model $ownerRecord, string $pageClass): ?string
    {
        /** @var \Domain\Shop\Product\Models\Product $ownerRecord */
        return (string) $ownerRecord->attributes()->count();
    }

    #[Override]
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->translateLabel()
                    ->required()
                    ->maxLength(255)
                    ->unique(
                        ignoreRecord: true,
                        modifyRuleUsing: fn (Unique $rule, Get $get) => $rule
                            ->where(
                                'product_uuid',
                                $this->ownerRecord->getKey()
                            )
                    ),

                Select::make('type')
                    ->translateLabel()
                    ->options(AttributeFieldType::class)
                    ->enum(AttributeFieldType::class)
                    ->required()
                    ->default(AttributeFieldType::text)
                    ->default(AttributeFieldType::text)
                    ->selectablePlaceholder(false)
                    ->afterStateUpdated(function (Set $set): void {
                        $set('prefix', null);
                        $set('suffix', null);
                    })
                    ->live(),

                TextInput::make('prefix')
                    ->translateLabel()
                    ->disabled(fn (Get $get): bool => $get('type') === AttributeFieldType::color_picker)
                    ->nullable()
                    ->string()
                    ->maxLength(3),

                TextInput::make('suffix')
                    ->translateLabel()
                    ->disabled(fn (Get $get): bool => $get('type') === AttributeFieldType::color_picker)
                    ->nullable()
                    ->string()
                    ->maxLength(3),
            ])
            ->columns(2);
    }

    #[Override]
    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->translateLabel()
                    ->sortable()
                    ->searchable(),

                TextColumn::make('type')
                    ->translateLabel()
                    ->badge()
                    ->sortable()
                    ->searchable(),

                TextColumn::make('prefix')
                    ->translateLabel()
                    ->sortable()
                    ->searchable()
                    ->default(new HtmlString('&mdash;')),

                TextColumn::make('suffix')
                    ->translateLabel()
                    ->sortable()
                    ->searchable()
                    ->default(new HtmlString('&mdash;')),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
