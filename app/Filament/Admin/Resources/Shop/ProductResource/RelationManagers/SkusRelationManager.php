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

use App\Filament\Admin\Resources\Shop\SkuStockResource\Schema\SkuStockSchema;
use Domain\Shop\Branch\Models\Branch;
use Domain\Shop\Product\Enums\AttributeFieldType;
use Domain\Shop\Product\Enums\SkuMinimumType;
use Domain\Shop\Product\Models\Attribute;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Override;

/**
 * @property-read \Domain\Shop\Product\Models\Product $ownerRecord
 */
class SkusRelationManager extends RelationManager
{
    #[\Override]
    protected static string $relationship = 'skus';

    #[Override]
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columns(3)
                    ->schema([
                        TextInput::make('code')
                            ->translateLabel()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->alphaDash()
                            ->helperText(trans('Only letters, numbers, dashes and underscores are allowed'))
                            ->columnSpan(2),
                        //                ->disabled(fn (Forms\Get $get): bool => $get('auto_generate_code')),

                        //            Forms\Components\Toggle::make('auto_generate_code')
                        //                ->translateLabel()
                        //                ->dehydrated(false)
                        //                ->reactive()
                        //                ->helperText(trans('If enabled, the code will be generated automatically'))
                        //                ->afterStateHydrated(
                        //                    fn (Forms\Components\Toggle $component) => $component->state(true)
                        //                ),

                        TextInput::make('price')
                            ->translateLabel()
                            ->money()
                            ->required()
                            ->numeric()
                            ->columnSpan(1),

                    ]),

                Section::make(trans('Attribute options'))
                    ->collapsible()
                    ->collapsed(fn (string $context) => $context === 'edit')
                    ->schema([

                        Repeater::make('attributeOptions')
                            ->translateLabel()
                            ->itemLabel(
                                function (array $state): ?string {

                                    if (blank($state['attribute_uuid'] ?? null) || blank($state['value'] ?? null)) {
                                        return null;
                                    }

                                    return Attribute::whereKey($state['attribute_uuid'])
                                        ->value('name').': '.$state['value'];
                                }
                            )
                            ->relationship()
                            ->collapsible()
                            ->collapsed(fn (string $context) => $context === 'edit')
                            ->cloneable()
                            ->orderColumn(config()->string('eloquent-sortable.order_column_name'))
                            ->reorderableWithButtons()
                            ->maxItems(fn () => $this->ownerRecord->attributes()->count())
                            ->schema(
                                [

                                    Select::make('attribute_uuid')
                                        ->translateLabel()
                                        ->required()
                                        ->relationship(
                                            'attribute',
                                            'name',
                                            modifyQueryUsing: fn (
                                                Builder $query
                                            ) => $query->whereBelongsTo($this->ownerRecord)
                                        )
                                        ->preload()
                                        ->searchable()
                                        ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                        ->afterStateUpdated(fn (Set $set) => $set('value', null)),

                                    Group::make()
                                        ->schema(function (Get $get): array {

                                            $attribute = Attribute::whereKey($get('attribute_uuid'))->first();
                                            $type = $attribute?->type;

                                            $field = match ($type) {
                                                AttributeFieldType::numeric,AttributeFieldType::text => TextInput::make('value')
                                                    ->translateLabel()
                                                    ->numeric(fn (Get $get) => $type === AttributeFieldType::numeric)
                                                    ->prefix(
                                                        fn (Get $get): ?string => $attribute?->prefix
                                                    )
                                                    ->suffix(
                                                        fn (Get $get): ?string => $attribute?->suffix
                                                    )
                                                    ->required()
                                                    ->live(),
                                                AttributeFieldType::color_picker => ColorPicker::make('value')
                                                    ->translateLabel()
                                                    ->required()
                                                    ->live(),
                                                null => TextInput::make('value')
                                                    ->translateLabel()
                                                    ->disabled()
                                            };

                                            return [$field];
                                        }),

                                ]
                            )
                            ->columns(2),

                    ]),

                Section::make(trans('Stocks'))
                    ->collapsible()
                    ->collapsed(fn (string $context) => $context === 'edit')
                    ->schema([
                        Repeater::make('skuStocks')
                            ->translateLabel()
//                            ->itemLabel(fn (array $state): string => trans(':branch: :type', [
//                                'branch' => Branch::whereKey($state['branch_uuid'])->value('name'),
//                                'type' => Str::headline($state['type'] ?? null),
//                            ]))
                            ->relationship()
                            ->maxItems(fn () => Branch::count())
                            ->schema(SkuStockSchema::schema(hasSku: false))
                            ->collapsible()
                            ->collapsed(fn (string $context) => $context === 'edit'),
                    ]),

                Section::make(trans('Other fields'))
                    ->collapsible()
                    ->collapsed(fn (string $context) => $context === 'edit')
                    ->schema([
                        TextInput::make('minimum')
                            ->translateLabel()
                            ->numeric()
                            ->minValue(1)
                            ->nullable(),

                        Select::make('minimum_type')
                            ->translateLabel()
                            ->options(SkuMinimumType::class)
                            ->enum(SkuMinimumType::class)
                            ->requiredWith('minimum'),

                        TextInput::make('maximum')
                            ->translateLabel()
                            ->numeric()
                            ->minValue(1)
                            ->nullable()
                            ->columnSpanFull(),
                    ])
                    ->columns(),

                Section::make(trans('Images'))
                    ->collapsible()
                    ->collapsed(fn (string $context) => $context === 'edit')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('image')
                            ->translateLabel()
                            ->hiddenLabel()
                            ->collection('image')
                            ->multiple()
                            ->reorderable()
                            ->maxFiles(5),
                    ]),

            ])
            ->columns(1);
    }

    #[Override]
    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('code')
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['attributeOptions.attribute']))
            ->columns([

                SpatieMediaLibraryImageColumn::make('image')
                    ->translateLabel()
                    ->collection('image')
                    ->conversion('thumb')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->circular(),

                TextColumn::make('code')
                    ->translateLabel()
                    ->sortable()
                    ->searchable(isIndividual: true),

                TextColumn::make('attribute_options_list')
                    ->translateLabel()
                    ->bulleted(),

                TextColumn::make('price')
                    ->translateLabel()
                    ->sortable()
                    ->money(),

                TextColumn::make('attribute_options_count')
                    ->translateLabel()
                    ->counts('attributeOptions')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([

            ])
            ->headerActions([
                CreateAction::make()
                    ->translateLabel()
                    ->tooltip(function (): ?string {
                        if ($this->ownerRecord->attributes()->doesntExist()) {
                            return trans('Create a sku first.');
                        }

                        return null;
                    }),
            ])
            ->recordActions([
                EditAction::make()
                    ->translateLabel(),
                DeleteAction::make()
                    ->translateLabel(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->translateLabel(),
                ]),
            ])
            ->emptyStateActions([
                CreateAction::make()
                    ->translateLabel()
                    ->tooltip(function (): ?string {
                        if ($this->ownerRecord->attributes()->doesntExist()) {
                            return trans('Create a sku first.');
                        }

                        return null;
                    }),
            ]);
    }
}
