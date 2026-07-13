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

namespace App\Filament\Admin\Resources\Shop\OrderResource\Pages;

use App\Filament\Admin\Resources\Shop\CustomerResource;
use App\Filament\Admin\Resources\Shop\OrderResource;
use App\Filament\Admin\Support\TenantHelper;
use App\Http\Controllers\Admin\OrderInvoiceDownloadController;
use App\Jobs\Order\GenerateOrderInvoiceJob;
use Domain\Shop\Order\Actions\PrintReceiptAction;
use Domain\Shop\Order\Enums\OrderPaymentMethod;
use Domain\Shop\Order\Enums\OrderPaymentStatus;
use Domain\Shop\Order\Enums\OrderStatus;
use Domain\Shop\Order\Models\Order;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Forms\Components\ToggleButtons;
use Filament\Infolists;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\HtmlString;
use Override;

/**
 * @property-read Order $record
 */
class ViewOrder extends ViewRecord
{
    #[\Override]
    protected static string $resource = OrderResource::class;

    #[Override]
    protected function resolveRecord(int|string $key): Model
    {
        /** @var Order $record */
        $record = Order::withTrashed()->findOrFail($key);

        $record->loadMissing([
            'orderItems' => function ($query) {
                $query->with('sku')->select('uuid', 'order_uuid', 'sku_uuid', 'sku_code', 'name', 'price', 'quantity', 'deleted_at');
            },
            'orderInvoices' => function (HasMany $query): void {
                $query->limit(1)->latest();
            },
        ]);

        return $record;
    }

    #[Override]
    protected function getHeaderActions(): array
    {
        return [
            Action::make('generate_invoice')
                ->translateLabel()
                ->icon(Heroicon::OutlinedDocumentText)
                ->hidden($this->record->trashed())
                ->action(function (Action $action) {
                    GenerateOrderInvoiceJob::dispatch($this->record);

                    $action->success();
                })
                ->successNotificationTitle(trans('Invoice queued for generation'))
                ->authorize('generateInvoice'),

            Action::make('download_latest_invoice')
                ->translateLabel()
                ->icon(Heroicon::OutlinedPrinter)
                ->hidden($this->record->trashed())
                ->visible(fn () => $this->record->orderInvoices->isNotEmpty())
                ->url(
                    fn (): string => action(
                        [OrderInvoiceDownloadController::class, 'invoice'],
                        ['orderInvoice' => $this->record->orderInvoices[0]]
                    ),
                    shouldOpenInNewTab: true
                )
                ->authorize('downloadInvoice'),

            Action::make('print_receipt')
                ->translateLabel()
                ->icon(Heroicon::OutlinedPrinter)
                ->hidden($this->record->trashed())
                ->successNotificationTitle(trans('Receipt sent to printer!'))
                ->failureNotificationTitle(trans('Failed to send receipt to printer!'))
                ->action(function (Action $action) {

                    try {
                        app(PrintReceiptAction::class)->execute($this->record);
                        $action->success();
                    } catch (Exception $e) {
                        report($e);
                        $action->failure();
                    }

                })
                ->authorize('printReceipt'),

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
        return self::staticInfolist($schema);
    }

    public static function staticInfolist(Schema $schema): Schema
    {
        return $schema->components([

            Group::make()

                ->columnSpan(2)
                ->schema([

                    Section::make()
                        ->columns()
                        ->schema([
                            TextEntry::make('receipt_number')
                                ->translateLabel()
                                ->copyable()
                                ->icon(Heroicon::OutlinedReceiptRefund),

                            TextEntry::make('customer.full_name')
                                ->translateLabel()
                                ->icon(Heroicon::OutlinedUser)
                                ->url(
                                    function (Order $record): ?string {

                                        if ($record->customer === null || TenantHelper::getBranch() !== null) {
                                            return null;
                                        }

                                        return CustomerResource::can('update', $record->customer)
                                            ? CustomerResource::getUrl('edit', [$record->customer])
                                            : null;
                                    },
                                    shouldOpenInNewTab: true
                                )
                                ->placeholder(trans('Guest')),

                            TextEntry::make('branch.name')
                                ->translateLabel()
                                ->visible(fn (): bool => config()->boolean('app-default.branch_feature_enabled'))
                                ->icon(Heroicon::OutlinedBuildingStorefront),

                            TextEntry::make('claim_at')
                                ->translateLabel()
                                ->dateTime()
                                ->icon(Heroicon::OutlinedTruck),

                            TextEntry::make('claim_type')
                                ->translateLabel(),

                            TextEntry::make('payment_method')
                                ->translateLabel()
                                ->default(new HtmlString('&mdash;'))
                                ->suffixAction(
                                    Action::make('update_payment_method')
                                        ->translateLabel()
                                        ->icon(Heroicon::OutlinedPencil)
                                        ->slideOver()
                                        ->button()
                                        ->authorize(fn (Order $record) => Gate::allows('updatePaymentMethod', $record))
                                        ->schema(fn (Order $record) => [
                                            ToggleButtons::make('payment_method')
                                                ->translateLabel()
                                                ->default($record->payment_method)
                                                ->inline()
                                                ->options(OrderPaymentMethod::class)
                                                ->enum(OrderPaymentMethod::class)
                                                ->required(),
                                        ])
                                        ->successNotificationTitle(
                                            trans('Payment method updated successfully!')
                                        )
                                        ->action(function (Action $action, Order $record, array $data): void {
                                            $record->update(['payment_method' => $data['payment_method']]);

                                            $action->success();
                                        })
                                ),

                            TextEntry::make('payment_status')
                                ->translateLabel()
                                ->suffixAction(
                                    Action::make('update_payment_status')
                                        ->translateLabel()
                                        ->icon(Heroicon::OutlinedPencil)
                                        ->button()
                                        ->slideOver()
                                        ->authorize(fn (Order $record) => Gate::allows('updatePaymentStatus', $record))
                                        ->schema(fn (Order $record) => [
                                            ToggleButtons::make('payment_status')
                                                ->translateLabel()
                                                ->default($record->payment_status)
                                                ->inline()
                                                ->options(OrderPaymentStatus::class)
                                                ->enum(OrderPaymentStatus::class)
                                                ->required(),
                                        ])
                                        ->successNotificationTitle(
                                            trans('Payment status updated successfully!')
                                        )
                                        ->action(function (Action $action, Order $record, array $data): void {
                                            $record->update(['payment_status' => $data['payment_status']]);

                                            $action->success();

                                        }),
                                ),

                            TextEntry::make('status')
                                ->translateLabel()
                                ->suffixAction(
                                    Action::make('update_status')
                                        ->translateLabel()
                                        ->icon(Heroicon::OutlinedPencil)
                                        ->button()
                                        ->slideOver()
                                        ->authorize(fn (Order $record) => Gate::allows('updateStatus', $record))
                                        ->schema(fn (Order $record) => [
                                            ToggleButtons::make('status')
                                                ->translateLabel()
                                                ->default($record->status)
                                                ->inline()
                                                ->options(OrderStatus::class)
                                                ->enum(OrderStatus::class)
                                                ->required(),
                                        ])
                                        ->successNotificationTitle(
                                            trans('Status updated successfully!')
                                        )
                                        ->action(function (Action $action, Order $record, array $data): void {
                                            $record->update(['status' => $data['status']]);

                                            $action->success();

                                        })
                                ),
                        ]),

                    Section::make()
                        ->schema([
                            TextEntry::make('notes')
                                ->placeholder(new HtmlString('&mdash;')),
                        ]),

                    Section::make()
                        ->schema([
                            RepeatableEntry::make('orderItems')
                                ->translateLabel()
                                ->columns(5)
                                ->schema([
                                    TextEntry::make('name')
                                        ->label(trans('Product  name'))
                                        ->icon(Heroicon::OutlinedShoppingBag),

                                    TextEntry::make('sku.code')
                                        ->translateLabel()
                                        ->icon(Heroicon::OutlinedShoppingBag),

                                    TextEntry::make('minimum')
                                        ->translateLabel(),

                                    TextEntry::make('price')
                                        ->translateLabel()
                                        ->icon(Heroicon::OutlinedCurrencyDollar)
                                        ->money(),

                                    TextEntry::make('quantity')
                                        ->translateLabel(),
                                ]),
                        ]),
                ]),

            Group::make()
                ->columnSpan(1)
                ->schema([
                    Section::make()
                        ->schema([

                            //                            Infolists\Components\TextEntry::make('delivery_price')
                            //                                ->icon(Heroicon::Outlined-s-truck'),

                            TextEntry::make('total_price')
                                ->translateLabel()
                                ->icon(Heroicon::OutlinedCurrencyDollar)
                                ->state(fn (Order $record) => $record->total_price)
                                ->money(),

                        ]),
                    Section::make()
                        ->schema([

                            TextEntry::make('purchased_at')
                                ->translateLabel()
                                ->dateTime()
                                ->sinceTooltip()
                                ->icon(Heroicon::OutlinedCalendar),

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
        ])
            ->columns(3);
    }
}
