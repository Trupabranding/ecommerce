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

namespace Domain\Shop\Order\Actions;

use App\Settings\SiteSettings;
use Brick\Math\RoundingMode;
use Domain\Shop\Order\Models\Order;
use Illuminate\Support\Str;
use LaravelDaily\Invoices\Classes\Buyer;
use LaravelDaily\Invoices\Invoice;

final readonly class OrderInvoiceAction
{
    public const string FOLDER = 'invoices';

    public function __construct(private SiteSettings $siteSettings) {}

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Exception
     */
    public function execute(Order $order): Invoice
    {
        $invoice = Invoice::make()
            ->currencyCode(config()->string('app-default.currency'))
            ->currencySymbol(config()->string('app-default.currency_symbol'))
            ->currencyFraction('cents.')
            ->currencyFormat('{SYMBOL}{VALUE}')
            ->currencyThousandsSeparator(',');

        foreach ($order->orderItems as $orderItem) {
            $invoice->addItem(
                $invoice::makeItem()
                    ->title($orderItem->name)
                    ->description(Str::limit($orderItem->description ?? ''))
                    ->pricePerUnit($orderItem->price)
                    ->subTotalPrice(
                        moneyAmountToFloat(
                            money($orderItem->price)
                                ->multipliedBy(
                                    $orderItem->quantity,
                                    RoundingMode::Down
                                )
                        )
                    )
                    ->quantity($orderItem->quantity)
                //                    ->units('piece')
                    ->discount($orderItem->discount_price)
                //                ->units()

                //            if ($orderItem->description !== null) {
                //                $item->description((string) Str::of($orderItem->description)->stripTags());
                //            }
            );
        }

        if (money($order->delivery_price)->isGreaterThan(0)) {
            $invoice->addItem(
                $invoice::makeItem()
                    ->title(trans('Delivery Fee'))
                    ->subTotalPrice($order->delivery_price)
            );

        }

        if (filled($customer = $order->customer)) {

            $buyer = new Buyer([
                'name' => $customer->full_name,
                'address' => 'TODO: address', // TODO: address
                'custom_fields' => [
                    'email' => $customer->email,
                    'mobile' => $customer->mobile ?? 'n/a',
                    'landline' => $customer->landline ?? 'n/a',
                    'order number' => $order->receipt_number,
                ],
            ]);
        } else {
            $buyer = new Buyer([
                'name' => 'Guest',
            ]);
        }

        return $invoice
            ->seller(
                $invoice::makeParty([
                    'name' => $this->siteSettings->name,
                    'address' => $this->siteSettings->address,
                ])
            )
            ->buyer($buyer)
            ->totalAmount($order->total_price)
            ->status($order->payment_status->getLabel())
            ->filename(sprintf(
                // invoices/ORDER/ORDER_TIMESTAMP_invoice
                '%s/%s/%s_%s_invoice',
                self::FOLDER,
                $order->receipt_number,
                $order->receipt_number,
                $order->updated_at->timestamp ?? now()->timestamp,
            ))
            ->notes(
                'Test Note'
            );
    }
}
