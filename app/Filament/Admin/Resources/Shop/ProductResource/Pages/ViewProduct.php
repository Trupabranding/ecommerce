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

namespace App\Filament\Admin\Resources\Shop\ProductResource\Pages;

use App\Filament\Admin\Resources\Shop\ProductResource;
use App\Filament\Admin\Resources\Shop\SkuStockResource;
use Domain\Shop\Product\Models\Product;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;
use Override;

/**
 * @property-read Product $record
 */
class ViewProduct extends ViewRecord
{
    #[\Override]
    protected static string $resource = ProductResource::class;

    #[Override]
    protected function resolveRecord(int|string $key): Model
    {
        /** @var Product $record */
        $record = Product::withTrashed()->findOrFail($key);

        $record->loadMissing([
            'category:uuid,name,parent_uuid',
            'brand:uuid,name',
            'skus:uuid,product_uuid,code,price',
            'skus.skuStocks:uuid,sku_uuid,quantity',
            'media',
            'tags',
        ]);

        return $record;
    }

    #[Override]
    protected function getHeaderActions(): array
    {
        return [
            Action::make('edit')
                ->translateLabel()
                ->icon(Heroicon::OutlinedPencil)
                ->url(fn (): string => ProductResource::getUrl('edit', [$this->record]))
                ->button(),

            DeleteAction::make()
                ->translateLabel(),
            RestoreAction::make()
                ->translateLabel(),
            ForceDeleteAction::make()
                ->translateLabel(),
        ];
    }

    #[Override]
    public function infolist(Schema $schema): Schema
    {
        return $schema->components([

            Group::make()
                ->columnSpan(2)
                ->schema([

                    Section::make(trans('Product Details'))
                        ->columns()
                        ->schema([
                            TextEntry::make('name')
                                ->translateLabel()
                                ->icon(Heroicon::OutlinedShoppingBag),

                            TextEntry::make('parent_sku')
                                ->translateLabel()
                                ->copyable()
                                ->icon(Heroicon::OutlinedTag),

                            TextEntry::make('status')
                                ->translateLabel()
                                ->badge()
                                ->icon(Heroicon::OutlinedCheckCircle),

                            TextEntry::make('category.name_with_parent')
                                ->translateLabel()
                                ->placeholder(new HtmlString('&mdash;'))
                                ->icon(Heroicon::OutlinedFolderOpen),

                            TextEntry::make('brand.name')
                                ->translateLabel()
                                ->placeholder(new HtmlString('&mdash;'))
                                ->icon(Heroicon::OutlinedGift),

                            TextEntry::make('tags')
                                ->translateLabel()
                                ->placeholder(new HtmlString('&mdash;'))
                                ->formatStateUsing(
                                    fn (Product $record) => $record->tags
                                        ->pluck('name')
                                        ->join(', ')
                                )
                                ->icon(Heroicon::OutlinedTag),
                        ]),

                    Section::make(trans('Description'))
                        ->schema([
                            TextEntry::make('description')
                                ->translateLabel()
                                ->html()
                                ->placeholder(new HtmlString('&mdash;')),
                        ]),

                    Section::make(trans('Images'))
                        ->schema([
                            SpatieMediaLibraryImageEntry::make('image')
                                ->hiddenLabel()
                                ->collection('image')
                                ->conversion('list'),
                        ])
                        ->collapsible(),

                    Section::make(trans('Variants'))
                        ->schema([
                            RepeatableEntry::make('skus')
                                ->translateLabel()
                                ->columns(3)
                                ->schema([
                                    TextEntry::make('code')
                                        ->translateLabel()
                                        ->copyable()
                                        ->icon(Heroicon::OutlinedTag),

                                    TextEntry::make('price')
                                        ->translateLabel()
                                        ->icon(Heroicon::OutlinedCurrencyDollar)
                                        ->money(),

                                    TextEntry::make('stock_quantity')
                                        ->translateLabel()
                                        ->state(fn ($record) => $record->skuStocks->sum('quantity') ?? 0)
                                        ->icon(Heroicon::OutlinedCube),
                                ]),
                        ]),
                ]),

            Group::make()
                ->columnSpan(1)
                ->schema([
                    Section::make(trans('Stock Summary'))
                        ->schema([
                            TextEntry::make('skus_count')
                                ->label(trans('Total SKUs'))
                                ->state(fn (Product $record) => $record->skus->count())
                                ->icon(Heroicon::OutlinedTag),

                            TextEntry::make('total_stock')
                                ->label(trans('Total Stock'))
                                ->state(fn (Product $record) => $record->skus->sum(
                                    fn ($sku) => $sku->skuStocks->sum('quantity')
                                ))
                                ->icon(Heroicon::OutlinedCube),
                        ]),

                    Section::make()
                        ->schema([
                            TextEntry::make('created_at')
                                ->translateLabel()
                                ->dateTime()
                                ->sinceTooltip()
                                ->icon(Heroicon::OutlinedCalendar),

                            TextEntry::make('updated_at')
                                ->translateLabel()
                                ->dateTime()
                                ->sinceTooltip()
                                ->icon(Heroicon::OutlinedCalendar),

                            TextEntry::make('deleted_at')
                                ->translateLabel()
                                ->dateTime()
                                ->sinceTooltip()
                                ->icon(Heroicon::OutlinedCalendar),
                        ]),
                ]),

            Group::make()
                ->columnSpan(3)
                ->schema([

                    Section::make(trans('SKU Pricing Matrix'))
                        ->collapsible()
                        ->collapsed(false)
                        ->schema([
                            ViewEntry::make('skus')
                                ->view('components.sku-pricing-matrix')
                                ->viewData([
                                    'skus' => $this->record->skus,
                                    'skuStockResource' => SkuStockResource::class,
                                ]),
                        ]),

                    Section::make(trans('Activity Timeline'))
                        ->collapsible()
                        ->collapsed(true)
                        ->schema([
                            ViewEntry::make('activities')
                                ->view('components.product-activity-timeline')
                                ->viewData([
                                    'activities' => $this->record->activities()->latest()->get(),
                                ]),
                        ]),
                ]),
        ])
            ->columns(3);
    }
}
