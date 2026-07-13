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

namespace App\Filament\Admin\Resources\Shop;

use App\Filament\Admin\Resources\Access\ActivityResource\RelationManagers\ActivitiesRelationManager;
use App\Filament\Admin\Resources\Shop\ProductResource\Pages\CreateProduct;
use App\Filament\Admin\Resources\Shop\ProductResource\Pages\EditProduct;
use App\Filament\Admin\Resources\Shop\ProductResource\Pages\ListProducts;
use App\Filament\Admin\Resources\Shop\ProductResource\Pages\ViewProduct;
use App\Filament\Admin\Resources\Shop\ProductResource\RelationManagers\AttributesRelationManager;
use App\Filament\Admin\Resources\Shop\ProductResource\RelationManagers\SkusRelationManager;
use App\Settings\SkuStockSettings;
use Domain\Shop\Category\Models\Category;
use Domain\Shop\Category\Models\EloquentBuilder\CategoryEloquentBuilder;
use Domain\Shop\Product\Enums\ProductStatus;
use Domain\Shop\Product\Models\Product;
use Exception;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\SpatieTagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\SpatieTagsColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Number;
use Illuminate\Support\Str;
use Override;

class ProductResource extends Resource
{
    #[\Override]
    protected static ?string $model = Product::class;

    #[\Override]
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedBolt;

    #[\Override]
    protected static ?int $navigationSort = 1;

    #[\Override]
    protected static ?string $recordTitleAttribute = 'name';

    #[Override]
    public static function getNavigationGroup(): ?string
    {
        return trans('Shop');
    }

    #[Override]
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([

                \Filament\Schemas\Components\Group::make()
                    ->schema([

                        Section::make()
                            ->schema([

                                TextInput::make('name')
                                    ->translateLabel()
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->afterStateUpdated(
                                        function (Set $set, ?string $state, ?Product $record): void {
                                            if ($record !== null || $state === null) {
                                                return;
                                            }
                                            $set(
                                                'parent_sku',
                                                Str::kebab($state)
                                            );
                                        }
                                    )
                                    ->live(onBlur: true),

                                TextInput::make('parent_sku')
                                    ->translateLabel()
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->alphaDash()
                                    ->helperText(trans('Only letters, numbers, dashes and underscores are allowed')),

                                RichEditor::make('description')
                                    ->translateLabel()
                                    ->nullable()
                                    // ->string()
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),

                        Section::make(trans('Images'))
                            ->schema([
                                SpatieMediaLibraryFileUpload::make('image')
                                    ->hiddenLabel()
                                    ->collection('image')
                                    ->multiple()
                                    ->reorderable()
                                    ->maxFiles(9),
                            ])
                            ->collapsible(),

                    ])
                    ->columnSpan(['lg' => 2]),

                \Filament\Schemas\Components\Group::make()
                    ->schema([
                        Section::make(trans('Status'))
                            ->schema([

                                ToggleButtons::make('status')
                                    ->translateLabel()
                                    ->required()
                                    ->options(ProductStatus::class)
                                    ->enum(ProductStatus::class),

                            ]),

                        Section::make(trans('Associations'))
                            ->schema([

                                Select::make('category_uuid')
                                    ->translateLabel()
                                    ->nullable()
                                    ->relationship(
                                        'category',
                                        'name',
                                        function (CategoryEloquentBuilder $query) {

                                            $query->whereChild();
                                        }
                                    )
                                    ->getOptionLabelFromRecordUsing(
                                        fn (Category $record) => $record->name_with_parent
                                    )
                                    ->searchable()
                                    ->preload(),

                                Select::make('brand_uuid')
                                    ->translateLabel()
                                    ->nullable()
                                    ->relationship('brand', 'name')
                                    ->searchable()
                                    ->preload(),

                                SpatieTagsInput::make('tags')
                                    ->translateLabel(),
                            ]),

                        Section::make()
                            ->schema([
                                TextEntry::make('created_at')
                                    ->translateLabel()
                                    ->state(fn (Product $record): ?string => $record->created_at?->diffForHumans()),

                                TextEntry::make('updated_at')
                                    ->translateLabel()
                                    ->state(fn (Product $record): ?string => $record->updated_at?->diffForHumans()),
                            ])
                            ->hiddenOn('create'),
                    ])
                    ->columnSpan(['lg' => 1]),

            ])
            ->columns(3);
    }

    /** @throws Exception */
    #[Override]
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make(config()->string('eloquent-sortable.order_column_name'))
                    ->label('#')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                SpatieMediaLibraryImageColumn::make('image')
                    ->translateLabel()
                    ->collection('image')
                    ->conversion('thumb')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->circular(),

                TextColumn::make('parent_sku')
                    ->translateLabel()
                    ->wrap()
                    ->searchable(isIndividual: true)
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->copyable(),

                TextColumn::make('name')
                    ->translateLabel()
                    ->wrap()
                    ->searchable(isIndividual: true)
                    ->sortable(),

                TextColumn::make('category')
                    ->translateLabel()
                    ->badge()
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->formatStateUsing(
                        fn (Product $record) => $record->category?->name_with_parent
                    ),

                SpatieTagsColumn::make('tags')
                    ->translateLabel()
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('brand.name')
                    ->translateLabel()
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),

                TextColumn::make('skus.code')
                    ->label('Sku code')
                    ->translateLabel()
                    ->bulleted()
                    ->searchable(isIndividual: true)
                    ->toggleable()
                    ->color(Color::Blue)
                    ->copyable(),

                TextColumn::make('skus.price')
                    ->label('Sku price')
                    ->translateLabel()
                    ->bulleted()
                    ->searchable()
                    ->toggleable()
                    ->money(),

                // Tables\Columns\SelectColumn::make('status')
                TextColumn::make('status')
                    ->translateLabel()
                    ->badge()
                    ->sortable(),

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

                TextColumn::make('deleted_at')
                    ->translateLabel()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable()
                    ->since()
                    ->dateTimeTooltip(),
            ])
            ->filters([

                SelectFilter::make('category')
                    ->translateLabel()
                    ->relationship(
                        'category',
                        'name',
                        fn (CategoryEloquentBuilder $query) => $query->whereChild()
                    )
                    ->getOptionLabelFromRecordUsing(
                        fn (Category $record) => $record->name_with_parent
                    )
                    ->searchable()
                    ->preload(),

                SelectFilter::make('brand')
                    ->translateLabel()
                    ->relationship('brand', 'name')
                    ->searchable()
                    ->preload(),

                TrashedFilter::make()
                    ->translateLabel(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->translateLabel(),
                EditAction::make()
                    ->translateLabel(),
            ])
            ->defaultSort(config()->string('eloquent-sortable.order_column_name'))
            ->reorderable(config()->string('eloquent-sortable.order_column_name'))
            ->paginatedWhileReordering()
            ->groups([
                Group::make('category.name')
                    ->getTitleFromRecordUsing(fn (Product $record) => $record->category?->name_with_parent),
                'brand.name',
                'status',
            ]);
    }

    #[Override]
    public static function getRelations(): array
    {
        return [
            SkusRelationManager::class,
            AttributesRelationManager::class,
            ActivitiesRelationManager::class,
        ];
    }

    #[Override]
    public static function getPages(): array
    {
        return [
            'index' => ListProducts::route('/'),
            'create' => CreateProduct::route('/create'),
            'view' => ViewProduct::route('/{record}'),
            'edit' => EditProduct::route('/{record}/edit'),
        ];
    }

    #[Override]
    public static function getGloballySearchableAttributes(): array
    {
        return [
            'brand.name',
            'skus.code',
            'parent_sku',
            'name',
        ];
    }

    #[Override]
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        /** @var Product $record */
        return [
            'Brand' => $record->brand->name ?? '---',
            'Category' => $record->category->name_with_parent ?? '---',
            'Skus count' => (string) Number::format($record->skus_count ?? 0),
        ];
    }

    #[Override]
    public static function getNavigationBadgeTooltip(): ?string
    {
        return trans('There are products with warning stocks');
    }

    #[Override]
    public static function getNavigationBadge(): ?string
    {
        $count = once(
            fn () => Product::whereBaseOnStocksIsWarning()->count()
        );

        if ($count === 0) {
            return null;
        }

        return (string) $count;
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
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ])
            ->with([
                'category:uuid,name,parent_uuid',
                'brand:uuid,name',
                'skus:uuid,product_uuid,code,price',
                'media',
                'tags',
            ])
            ->withCount('skus');
    }
}
