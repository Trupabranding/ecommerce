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

namespace App\Filament\Admin\Resources\Shop;

use App\Filament\Admin\Resources\Access\ActivityResource\RelationManagers\ActivitiesRelationManager;
use App\Filament\Admin\Resources\Shop\SkuStockResource\Pages\CreateSkuStock;
use App\Filament\Admin\Resources\Shop\SkuStockResource\Pages\EditSkuStock;
use App\Filament\Admin\Resources\Shop\SkuStockResource\Pages\ListSkuStocks;
use App\Filament\Admin\Resources\Shop\SkuStockResource\Schema\SkuStockSchema;
use App\Settings\SkuStockSettings;
use Domain\Shop\Stock\Enums\SkuStockType;
use Domain\Shop\Stock\Models\EloquentBuilder\SkuStockEloquentBuilder;
use Domain\Shop\Stock\Models\SkuStock;
use Exception;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Override;

class SkuStockResource extends Resource
{
    #[\Override]
    protected static ?string $model = SkuStock::class;

    #[\Override]
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCalculator;

    #[\Override]
    protected static ?int $navigationSort = 7;

    #[Override]
    public static function getNavigationGroup(): ?string
    {
        return trans('Shop');
    }

    #[Override]
    public static function form(Schema $schema): Schema
    {
        return SkuStockSchema::form($schema);
    }

    /** @throws Exception */
    #[Override]
    public static function table(Table $table): Table
    {
        $branchFeatureEnabled = config()->boolean('app-default.branch_feature_enabled');

        return $table
            ->columns([

                TextColumn::make('sku.code')
                    ->translateLabel()
                    ->searchable(isIndividual: true)
                    ->sortable()
                    ->copyable(),

                TextColumn::make('sku.product.name')
                    ->translateLabel()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('branch.name')
                    ->translateLabel()
                    ->sortable()
                    ->visible($branchFeatureEnabled)
                    ->toggleable(),

                TextColumn::make('type')
                    ->translateLabel()
                    ->badge()
                    ->searchable()
                    ->sortable(),

                TextColumn::make('count')
                    ->translateLabel()
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->color(
                        fn (SkuStock $record) => $record->type === SkuStockType::base_on_stock
                            ? $record->isBaseOnStockWarning() ? Color::Red : Color::Green
                            : null
                    )
                    ->tooltip(
                        fn (SkuStock $record) => $record->type === SkuStockType::base_on_stock
                            ? $record->isBaseOnStockWarning() ? trans('Low stock warning') : null
                            : null
                    ),

                TextColumn::make('warning')
                    ->translateLabel()
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->state(
                        fn (SkuStock $record) => $record->type === SkuStockType::base_on_stock
                            ? $record->warning
                            : 'n/a'
                    ),

                TextColumn::make('updated_at')
                    ->translateLabel()
                    ->sortable()
                    ->since()
                    ->dateTimeTooltip(),

                TextColumn::make('created_at')
                    ->translateLabel()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable()
                    ->since()
                    ->dateTimeTooltip(),
            ])
            ->filters([
                SelectFilter::make('branch')
                    ->translateLabel()
                    ->relationship('branch', 'name')
                    ->visible($branchFeatureEnabled)
                    ->searchable()
                    ->preload(),

                SelectFilter::make('type')
                    ->translateLabel()
                    ->options(SkuStockType::class),

                //                Tables\Filters\SelectFilter::make('sku.product')
                //                    ->translateLabel()
                //                    ->relationship('sku.product', 'name')
                //                    ->searchable()
                //                    ->preload(),

                TernaryFilter::make('has_base_on_stocks_warning')
                    ->translateLabel()
                    ->queries(
                        true: fn (SkuStockEloquentBuilder $query) => $query->whereBaseOnStocksIsWarning(),
                        false: fn (SkuStockEloquentBuilder $query) => $query->whereBaseOnStocksIsNotWarning(),
                    ),
            ])
            ->recordActions([
                EditAction::make()
                    ->translateLabel(),
                ActionGroup::make([
                    DeleteAction::make()
                        ->translateLabel(),
                ]),
            ])
            ->defaultSort('updated_at', 'desc')
            ->groups(array_filter([
                $branchFeatureEnabled ? 'branch.name' : null,
                'sku.product.name',
                'type',
            ]));
    }

    #[Override]
    public static function getGloballySearchableAttributes(): array
    {
        if (! config()->boolean('app-default.branch_feature_enabled')) {
            return ['sku.code'];
        }

        return ['sku.code', 'branch.name'];
    }

    #[Override]
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        /** @var SkuStock $record */

        if (! config()->boolean('app-default.branch_feature_enabled')) {
            return [
                'Product' => $record->sku->product->name,
                'Sku code' => $record->sku->code,
            ];
        }

        return [
            'Branch' => $record->branch->name,
            'Product' => $record->sku->product->name,
            'Sku code' => $record->sku->code,
        ];
    }

    #[Override]
    public static function getPages(): array
    {
        return [
            'create' => CreateSkuStock::route('/create'),
            'index' => ListSkuStocks::route('/'),
            'edit' => EditSkuStock::route('/{record}/edit'),
        ];
    }

    #[Override]
    public static function getRelations(): array
    {
        return [ActivitiesRelationManager::class];
    }

    #[Override]
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withWhereHas('sku.product');
    }

    #[Override]
    public static function getNavigationBadge(): ?string
    {
        $count = SkuStock::whereBaseOnStocksIsWarning()->count();

        if ($count > 0) {
            return (string) $count;
        }

        return null;
    }

    #[Override]
    public static function getNavigationBadgeColor(): ?array
    {
        $count = self::getNavigationBadge();

        if ($count === null) {
            return null;
        }

        return app(SkuStockSettings::class)->getColor((int) $count);
    }

    #[Override]
    public static function getNavigationBadgeTooltip(): ?string
    {
        return trans('Low stock warning');
    }
}
