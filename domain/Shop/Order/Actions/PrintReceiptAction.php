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

namespace Domain\Shop\Order\Actions;

use App\Settings\SiteSettings;
use Domain\Shop\Order\Models\Order;
use Support\ReceiptPrinter\Data\ItemData;
use Support\ReceiptPrinter\Data\ReceiptPrinterData;
use Support\ReceiptPrinter\Data\StoreData;
use Support\ReceiptPrinter\ReceiptPrinter;

readonly class PrintReceiptAction
{
    public function __construct(
        private SiteSettings $siteSettings,
    ) {}

    public function execute(Order $order): void
    {
        $items = [];

        foreach ($order->orderItems as $orderItem) {
            $items[] = new ItemData(
                $orderItem->name.($orderItem->minimum === null ? '' : ' (min: '.$orderItem->minimum.')'),
                $orderItem->quantity,
                money($orderItem->price),
                money($orderItem->total_price),
            );
        }

        $data = (new ReceiptPrinterData)
            ->store(new StoreData(
                mid: 'TESTMID',
                name: $this->siteSettings->name,
                address: $order->branch->address ?? '',
                phone: $order->branch->phone ?? '',
                email: $order->branch->email ?? '',
                website: $order->branch->website ?? '',
            ))
            ->qrCode([
                'receipt_number' => $order->receipt_number,
            ])
            ->transactionId($order->receipt_number)
            ->items($items);

        new ReceiptPrinter($data)->send();
    }
}
