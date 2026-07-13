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
use App\Filament\Admin\Resources\Shop\CategoryResource\Pages\CreateCategory;
use App\Filament\Admin\Resources\Shop\CategoryResource\Pages\EditCategory;
use App\Filament\Admin\Resources\Shop\CategoryResource\Pages\ListCategories;
use Domain\Shop\Category\Exports\CategoryExporter;
use Domain\Shop\Category\Models\Category;
use Domain\Shop\Category\Models\EloquentBuilder\CategoryEloquentBuilder;
use Exception;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportBulkAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Override;

class CategoryResource extends Resource
{
    #[\Override]
    protected static ?string $model = Category::class;

    #[\Override]
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    #[\Override]
    protected static ?int $navigationSort = 4;

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
                Section::make()
                    ->schema([
                        TextInput::make('name')
                            ->translateLabel()
                            ->required()
                            ->maxValue(255),

                        Select::make('parent_uuid')
                            ->translateLabel()
                            ->relationship(
                                'parent',
                                'name',
                                fn (?Category $record, CategoryEloquentBuilder $query) => $query
                                    ->whereParent()
                                    ->when(
                                        $record,
                                        fn (CategoryEloquentBuilder $q, Category $category) => $q->whereKeyNot($category)
                                    )
                            )
                            ->searchable()
                            ->preload()
                            ->required(function (?Category $record): bool {
                                if ($record === null) {
                                    return false;
                                }

                                $record->loadProductCountWithTrashed();

                                return $record->parent_uuid !== null && $record->products_count > 0;
                            })
                            ->validationMessages([
                                'required' => trans('The parent category field is required when the category has associated products.'),
                            ])
                            ->disabled(function (?Category $record): bool {
                                if ($record === null) {
                                    return false;
                                }

                                $record->loadCount('children');

                                return $record->children_count > 0;
                            })
                            ->helperText(
                                function (?Category $record) {

                                    if (
                                        $record === null ||
                                        $record->loadCount('children')->children_count === 0
                                    ) {
                                        return null;
                                    }

                                    return trans_choice(
                                        'This category has 1 child and cannot have a parent.|This category has :count children and cannot have a parent.',
                                        $record->children_count ?? 0,
                                        [
                                            'count' => $record->children_count,
                                        ]
                                    );
                                }
                            ),

                        Toggle::make('is_visible')
                            ->translateLabel()
                            ->default(true),

                        RichEditor::make('description')
                            ->translateLabel()
                            ->nullable()
                            ->string(),

                        SpatieMediaLibraryFileUpload::make('image')
                            ->translateLabel()
                            ->collection('image'),
                    ])
                    ->columnSpan(['lg' => fn (?Category $record) => $record === null ? 3 : 2]),

                Section::make()
                    ->schema([
                        TextEntry::make('created_at')
                            ->translateLabel()
                            ->state(fn (Category $record): ?string => $record->created_at?->diffForHumans()),

                        TextEntry::make('updated_at')
                            ->translateLabel()
                            ->state(fn (Category $record): ?string => $record->updated_at?->diffForHumans()),
                    ])
                    ->columnSpan(['lg' => 1])
                    ->hiddenOn('create'),

                Section::make('Category Configuration')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Tabs::make('Configuration')
                            ->tabs([
                                Tab::make('Display Rules')
                                    ->schema([
                                        Select::make('configuration.display_rules.template')
                                            ->label('Display Template')
                                            ->options([
                                                'default' => 'Default',
                                                'minimal' => 'Minimal',
                                                'detailed' => 'Detailed',
                                            ])
                                            ->default('default'),

                                        Toggle::make('configuration.display_rules.show_product_count')
                                            ->label('Show Product Count')
                                            ->default(true),

                                        TextInput::make('configuration.display_rules.image_requirements.required_count')
                                            ->label('Required Images')
                                            ->numeric()
                                            ->default(1),

                                        TextInput::make('configuration.display_rules.image_requirements.min_dimensions')
                                            ->label('Minimum Image Dimensions')
                                            ->placeholder('300x300')
                                            ->default('300x300'),
                                    ])->columns(2),

                                Tab::make('Product Rules')
                                    ->schema([
                                        Toggle::make('configuration.product_rules.require_description')
                                            ->label('Require Description')
                                            ->default(true),

                                        TextInput::make('configuration.product_rules.require_images')
                                            ->label('Minimum Images Required')
                                            ->numeric()
                                            ->default(1),
                                    ])->columns(2),

                                Tab::make('Pricing Rules')
                                    ->schema([
                                        TextInput::make('configuration.pricing_rules.tax_rate_override')
                                            ->label('Tax Rate Override (%)')
                                            ->numeric()
                                            ->step(0.01)
                                            ->placeholder('Leave empty for global setting'),

                                        Toggle::make('configuration.pricing_rules.allow_bulk_discounts')
                                            ->label('Allow Bulk Discounts'),

                                        TextInput::make('configuration.pricing_rules.min_price_threshold')
                                            ->label('Minimum Price Threshold')
                                            ->numeric()
                                            ->placeholder('Leave empty for no limit'),
                                    ])->columns(2),

                                Tab::make('Inventory Rules')
                                    ->schema([
                                        Toggle::make('configuration.inventory_rules.track_by_sku')
                                            ->label('Track by SKU')
                                            ->default(true),

                                        Toggle::make('configuration.inventory_rules.allow_backorders')
                                            ->label('Allow Backorders')
                                            ->default(false),

                                        TextInput::make('configuration.inventory_rules.low_stock_threshold_override')
                                            ->label('Low Stock Threshold Override')
                                            ->numeric()
                                            ->placeholder('Leave empty for global setting'),

                                        Toggle::make('configuration.inventory_rules.show_stock_status')
                                            ->label('Show Stock Status')
                                            ->default(true),
                                    ])->columns(2),

                                Tab::make('SEO Rules')
                                    ->schema([
                                        TextInput::make('configuration.seo_rules.meta_title_template')
                                            ->label('Meta Title Template')
                                            ->placeholder('{category_name} - Buy at {store_name}'),

                                        TextInput::make('configuration.seo_rules.meta_description_template')
                                            ->label('Meta Description Template')
                                            ->placeholder('Browse our {category_name} collection'),

                                        TextInput::make('configuration.seo_rules.focus_keyword')
                                            ->label('Focus Keyword'),
                                    ])->columns(2),
                            ]),
                    ])
                    ->columnSpan(['lg' => 3]),
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

                TextColumn::make('name')
                    ->translateLabel()
                    ->searchable()
                    ->sortable(),

                TextColumn::make('parent.name')
                    ->translateLabel()
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                CheckboxColumn::make('is_visible')
                    ->translateLabel()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('products_count')
                    ->translateLabel()
                    ->counts('products')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

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
                TernaryFilter::make('is_visible')
                    ->translateLabel(),
                TrashedFilter::make()
                    ->translateLabel(),
            ])
            ->recordActions([
                EditAction::make()
                    ->translateLabel(),
                ActionGroup::make([
                    DeleteAction::make()
                        ->translateLabel(),
                    RestoreAction::make()
                        ->translateLabel(),
                    ForceDeleteAction::make()
                        ->translateLabel(),
                ]),
            ])
            ->toolbarActions([
                ExportBulkAction::make()
                    ->translateLabel()
                    ->exporter(CategoryExporter::class)
                    ->authorize('exportAny'),
                //                    ->withActivityLog(),
            ])
            ->defaultSort(config()->string('eloquent-sortable.order_column_name'))
            ->reorderable(config()->string('eloquent-sortable.order_column_name'))
            ->paginatedWhileReordering()
            ->groups([
                'parent.name',
                Group::make('is_visible')
                    ->getTitleFromRecordUsing(
                        fn (Category $record) => $record->is_visible
                            ? trans('Yes')
                            : trans('No')
                    ),
            ])
            ->groups([
                'is_visible',
            ]);
    }

    #[Override]
    public static function getPages(): array
    {
        return [
            'index' => ListCategories::route('/'),
            'create' => CreateCategory::route('/create'),
            'edit' => EditCategory::route('/{record}/edit'),
        ];
    }

    #[Override]
    public static function getRelations(): array
    {
        return [
            ActivitiesRelationManager::class,
        ];
    }

    #[Override]
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ])
            ->with([
                'parent:uuid,name',
                'media',
            ])
            ->withCount('products');
    }
}
