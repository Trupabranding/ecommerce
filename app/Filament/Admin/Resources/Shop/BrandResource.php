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
use App\Filament\Admin\Resources\Shop\BrandResource\Pages\CreateBrand;
use App\Filament\Admin\Resources\Shop\BrandResource\Pages\EditBrand;
use App\Filament\Admin\Resources\Shop\BrandResource\Pages\ListBrands;
use Domain\Shop\Brand\Models\Brand;
use Exception;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Number;
use Override;

class BrandResource extends Resource
{
    #[\Override]
    protected static ?string $model = Brand::class;

    #[\Override]
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedBookmarkSquare;

    #[\Override]
    protected static ?int $navigationSort = 5;

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
                            ->unique(ignoreRecord: true),

                        SpatieMediaLibraryFileUpload::make('image')
                            ->translateLabel()
                            ->collection('image')
                            ->multiple()
                            ->reorderable()
                            ->maxFiles(5),
                    ])
                    ->columnSpan(['lg' => fn (?Brand $record) => $record === null ? 3 : 2]),

                Section::make()
                    ->schema([
                        TextEntry::make('created_at')
                            ->translateLabel()
                            ->state(fn (Brand $record): ?string => $record->created_at?->diffForHumans()),

                        TextEntry::make('updated_at')
                            ->translateLabel()
                            ->state(fn (Brand $record): ?string => $record->updated_at?->diffForHumans()),
                    ])
                    ->columnSpan(['lg' => 1])
                    ->hiddenOn('create'),

                Section::make('Brand Configuration')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Tabs::make('Configuration')
                            ->tabs([
                                Tab::make('Display Settings')
                                    ->schema([
                                        ColorPicker::make('configuration.display_settings.brand_color')
                                            ->label('Brand Color')
                                            ->default('#000000'),

                                        Toggle::make('configuration.display_settings.display_logo')
                                            ->label('Display Logo')
                                            ->default(true),

                                        Textarea::make('configuration.display_settings.brand_story')
                                            ->label('Brand Story')
                                            ->rows(4)
                                            ->helperText('Tell your brand story in a compelling way'),
                                    ]),

                                Tab::make('Pricing Rules')
                                    ->schema([
                                        TextInput::make('configuration.pricing_rules.minimum_margin_percent')
                                            ->label('Minimum Margin Percent (%)')
                                            ->numeric()
                                            ->step(0.01)
                                            ->default(20),

                                        TextInput::make('configuration.pricing_rules.maximum_discount_percent')
                                            ->label('Maximum Discount Percent (%)')
                                            ->numeric()
                                            ->step(0.01)
                                            ->default(10),

                                        TextInput::make('configuration.pricing_rules.suggested_retail_markup')
                                            ->label('Suggested Retail Markup (%)')
                                            ->numeric()
                                            ->step(0.01)
                                            ->placeholder('Leave empty for no suggestion'),
                                    ])->columns(2),

                                Tab::make('Requirements')
                                    ->schema([
                                        Select::make('configuration.requirements.requires_certification')
                                            ->label('Certification Required')
                                            ->options([
                                                'none' => 'None',
                                                'ISO' => 'ISO',
                                                'FDA' => 'FDA',
                                                'UL' => 'UL',
                                            ])
                                            ->default('none'),

                                        Toggle::make('configuration.requirements.allow_variants')
                                            ->label('Allow Product Variants')
                                            ->default(true),

                                        Select::make('configuration.requirements.quality_tier')
                                            ->label('Quality Tier')
                                            ->options([
                                                'standard' => 'Standard',
                                                'premium' => 'Premium',
                                                'luxury' => 'Luxury',
                                            ])
                                            ->default('standard'),
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
            ->defaultSort(config()->string('eloquent-sortable.order_column_name'))
            ->reorderable(config()->string('eloquent-sortable.order_column_name'))
            ->paginatedWhileReordering();
    }

    #[Override]
    public static function getRelations(): array
    {
        return [
            ActivitiesRelationManager::class,
        ];
    }

    #[Override]
    public static function getPages(): array
    {
        return [
            'index' => ListBrands::route('/'),
            'create' => CreateBrand::route('/create'),
            'edit' => EditBrand::route('/{record}/edit'),
        ];
    }

    #[Override]
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ])
            ->with('media')
            ->withCount('products');
    }

    #[Override]
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        /** @var Brand $record */
        return [
            'Product count' => (string) Number::format($record->products_count ?? 0),
        ];
    }
}
