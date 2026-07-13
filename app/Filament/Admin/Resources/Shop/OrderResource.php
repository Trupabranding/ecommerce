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
use App\Filament\Admin\Resources\Shop\CustomerResource\RelationManagers\OrdersRelationManager as CustomerOrdersRelationManager;
use App\Filament\Admin\Resources\Shop\OrderResource\Pages\CreateOrder;
use App\Filament\Admin\Resources\Shop\OrderResource\Pages\ListOrders;
use App\Filament\Admin\Resources\Shop\OrderResource\Pages\ViewOrder;
use App\Filament\Admin\Resources\Shop\OrderResource\RelationManagers\OrderInvoicesRelationManager;
use Domain\Shop\Order\Enums\ClaimType;
use Domain\Shop\Order\Enums\OrderPaymentMethod;
use Domain\Shop\Order\Enums\OrderPaymentStatus;
use Domain\Shop\Order\Enums\OrderStatus;
use Domain\Shop\Order\Exports\OrderExporter;
use Domain\Shop\Order\Models\Order;
use Exception;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\ExportBulkAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\Summarizers\Average;
use Filament\Tables\Columns\Summarizers\Count;
use Filament\Tables\Columns\Summarizers\Range;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Query\Builder as IlluminateQueryBuilder;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Override;

class OrderResource extends Resource
{
    #[\Override]
    protected static ?string $model = Order::class;

    #[\Override]
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingBag;

    #[\Override]
    protected static ?int $navigationSort = 3;

    #[\Override]
    protected static ?string $recordTitleAttribute = 'receipt_number';

    #[Override]
    public static function getNavigationGroup(): ?string
    {
        return trans('Shop');
    }

    /** @throws Exception */
    #[Override]
    public static function table(Table $table): Table
    {
        $branchFeatureEnabled = config()->boolean('app-default.branch_feature_enabled');

        $countSummarize = fn (string $column, array $enumCases): array => collect($enumCases)->map(
            fn (ClaimType|OrderPaymentMethod|OrderPaymentStatus|OrderStatus $enum) => Count::make($enum->value)
                ->label(trans(Str::headline($enum->value)))
                ->query(fn (IlluminateQueryBuilder $query) => $query
                    ->where($column, $enum))
        )->toArray();

        return $table
            ->columns([
                TextColumn::make('receipt_number')
                    ->translateLabel()
                    ->searchable(isIndividual: true)
                    ->sortable()
                    ->copyable(),

                TextColumn::make('customer.full_name')
                    ->translateLabel()
                    ->description(fn (Order $record) => $record->customer?->email)
                    ->visibleOn([
                        ListOrders::class,
                    ])
                    ->searchable(['first_name', 'last_name'], isIndividual: true)
                    ->sortable(['first_name', 'last_name'])
//                    ->url(
//                        fn (Order $record) => CustomerResource::canView($record->customer)
//                            ? CustomerResource::getUrl('edit', [$record->customer])
//                            : null,
//                    )
                    ->wrap()
                    ->placeholder(trans('Guest')),

                TextColumn::make('total_price')
                    ->translateLabel()
                    ->money()
                    ->sortable()
                    ->summarize([
                        Average::make()
                            ->translateLabel()
                            ->money(divideBy: 100),
                        // "filament/filament": "^3.2.36",
                        // Filament\Tables\Columns\Summarizers\Summarizer::Filament\Tables\Columns\Summarizers\Concerns\{closure}(): Return value must be of type ?string, array returned
                        //                        Range::make()
                        //                            ->translateLabel()
                        //                            ->money(divideBy: 100),
                        Sum::make()
                            ->translateLabel()
                            ->money(divideBy: 100),
                    ]),

                TextColumn::make('branch.name')
                    ->translateLabel()
                    ->sortable()
                    ->visible($branchFeatureEnabled)
                    ->toggleable(),

                TextColumn::make('order_items_count')
                    ->translateLabel()
                    ->counts('orderItems')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),

                TextColumn::make('claim_type')
                    ->translateLabel()
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable()
                    ->default(new HtmlString('&mdash;'))
                    ->summarize($countSummarize('claim_type', ClaimType::cases())),

                TextColumn::make('payment_method')
                    ->translateLabel()
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable()
                    ->default(new HtmlString('&mdash;'))
                    ->summarize($countSummarize('payment_method', OrderPaymentMethod::cases())),

                TextColumn::make('payment_status')
                    ->translateLabel()
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable()
                    ->summarize($countSummarize('payment_status', OrderPaymentStatus::cases())),

                TextColumn::make('status')
                    ->translateLabel()
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable()
                    ->summarize($countSummarize('status', OrderStatus::cases())),

                TextColumn::make('admin.name')
                    ->translateLabel()
                    ->visibleOn([
                        ListOrders::class,
                        CustomerOrdersRelationManager::class,
                    ])
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),

                TextColumn::make('claim_at')
                    ->translateLabel()
                    ->sortable()
                    ->dateTime()
                    ->sinceTooltip(),

                TextColumn::make('purchased_at')
                    ->translateLabel()
                    ->toggleable()
                    ->sortable()
                    ->summarize(
                        Range::make()->minimalDateTimeDifference()
                            ->translateLabel()
                    )
                    ->dateTime()
                    ->sinceTooltip(),

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
                SelectFilter::make('branch')
                    ->translateLabel()
                    ->relationship('branch', 'name')
                    ->visible($branchFeatureEnabled)
                    ->searchable()
                    ->preload(),

                SelectFilter::make('claim_type')
                    ->translateLabel()
                    ->options(ClaimType::class),

                SelectFilter::make('payment_method')
                    ->translateLabel()
                    ->options(OrderPaymentMethod::class),

                SelectFilter::make('payment_status')
                    ->translateLabel()
                    ->options(OrderPaymentStatus::class),

                TrashedFilter::make()
                    ->translateLabel(),
            ])
            ->recordActions([
                ViewAction::make()
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
                    ->exporter(OrderExporter::class)
                    ->authorize('exportAny'),
                //                    ->withActivityLog(),
            ])
            ->defaultSort('updated_at', 'desc')
            ->groups(array_filter([
                $branchFeatureEnabled ? 'branch.name' : null,
                'customer.email',
                'payment_method',
                'payment_status',
                'status',
                Group::make('created_at')
                    ->collapsible()
                    ->date(),
                Group::make('updated_at')
                    ->collapsible()
                    ->date(),
            ]));
    }

    #[Override]
    public static function getRelations(): array
    {
        return [
            OrderInvoicesRelationManager::class,
            ActivitiesRelationManager::class,
        ];
    }

    #[Override]
    public static function getPages(): array
    {
        return [
            'index' => ListOrders::route('/'),
            'create' => CreateOrder::route('/create'),
            'view' => ViewOrder::route('/{record}'),
        ];
    }

    #[Override]
    public static function getNavigationBadgeTooltip(): ?string
    {
        return trans('There are new pending orders.');
    }

    #[Override]
    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    #[Override]
    public static function getNavigationBadge(): ?string
    {
        $count = Order::whereStatus(OrderStatus::pending)->count();

        if ($count === 0) {
            return null;
        }

        return (string) $count;
    }

    #[Override]
    public static function getGloballySearchableAttributes(): array
    {
        return ['receipt_number', 'customer.first_name', 'customer.last_name'];
    }

    #[Override]
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        /** @var Order $record */
        return [
            'Customer' => $record->customer->full_name ?? trans('Guest'),
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
                'customer:uuid,first_name,last_name,email',
                'branch:uuid,name',
                'orderItems:uuid,order_uuid,sku_code,name,price,quantity',
            ])
            ->withCount('orderItems');
    }
}
