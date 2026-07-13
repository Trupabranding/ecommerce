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

namespace App\Filament\Admin\Resources\Shop\SkuStockResource\Schema;

use Domain\Shop\Branch\Models\Branch;
use Domain\Shop\Stock\Enums\SkuStockType;
use Domain\Shop\Stock\Models\SkuStock;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Unique;

final class SkuStockSchema
{
    private function __construct() {}

    public static function form(Schema $schema, ?Branch $tenantBranch = null): Schema
    {
        return $schema
            ->components([

                Section::make()
                    ->schema(self::schema(tenantBranch: $tenantBranch))
                    ->columns(2)
                    ->columnSpan(['lg' => fn (?SkuStock $record) => $record === null ? 3 : 2]),

                Section::make()
                    ->schema([
                        TextEntry::make('created_at')
                            ->translateLabel()
                            ->state(fn (SkuStock $record): ?string => $record->created_at?->diffForHumans()),

                        TextEntry::make('updated_at')
                            ->translateLabel()
                            ->state(fn (SkuStock $record): ?string => $record->updated_at?->diffForHumans()),

                    ])
                    ->columnSpan(['lg' => 1])
                    ->hiddenOn('create'),
            ])
            ->columns(3);
    }

    public static function schema(bool $hasSku = true, ?Branch $tenantBranch = null): array
    {
        $branchFeatureEnabled = config()->boolean('app-default.branch_feature_enabled');

        return [
            Select::make('sku_uuid')
                ->translateLabel()
                ->relationship('sku', 'code')
                ->searchable()
                ->preload()
                ->required()
                ->unique(
                    ignoreRecord: true,
                    modifyRuleUsing: fn (Unique $rule, Get $get) => $rule
                        ->where(
                            'branch_uuid',
                            $tenantBranch?->getKey() ?? $get('branch_uuid')
                        )
                )
                ->validationMessages([
                    'unique' => fn (Get $get) => trans('The :attribute is already in stock with branch.'),
                ])
                ->disabledOn('edit')
                ->visible($hasSku),

            Select::make('branch_uuid')
                ->translateLabel()
                ->relationship('branch', 'name')
                ->searchable()
                ->preload()
                ->required()
                ->visible($branchFeatureEnabled)
                ->disabled(fn (?SkuStock $record) => $record !== null || $tenantBranch !== null)
                ->default($tenantBranch?->getKey() ?? function () use ($branchFeatureEnabled): ?string {
                    if (! $branchFeatureEnabled) {
                        return Branch::query()->value('uuid');
                    }

                    return null;
                })
                ->disableOptionsWhenSelectedInSiblingRepeaterItems(),

            TextInput::make('count')
                ->translateLabel()
                ->numeric()
                ->minValue(0)
                ->maxValue(500_000)
                ->required(fn (Get $get) => $get('type') === SkuStockType::base_on_stock)
                ->disabled(fn (Get $get) => $get('type') !== SkuStockType::base_on_stock)
                ->helperText(trans('Required if type is base on stock.')),

            TextInput::make('warning')
                ->translateLabel()
                ->numeric()
                ->minValue(0)
                ->maxValue(500_000)
                ->required(fn (Get $get) => $get('type') === SkuStockType::base_on_stock)
                ->disabled(fn (Get $get) => $get('type') !== SkuStockType::base_on_stock)
                ->helperText(trans('Get warning when reach the specified amount of count.')),

            ToggleButtons::make('type')
                ->translateLabel()
                ->inline()
                ->options(SkuStockType::class)
                ->enum(SkuStockType::class)
                ->required()
                ->reactive()
                ->columnSpanFull(),
        ];
    }
}
