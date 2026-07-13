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

namespace Domain\Shop\Order\Observers;

use App\Observers\LogAttemptDeleteResource;
use Domain\Shop\Order\Actions\CalculateOrderItemTotalPriceAction;
use Domain\Shop\Order\Actions\GetPaidQuantityAction;
use Domain\Shop\Order\DataTransferObjects\ItemWithMinMaxData;
use Domain\Shop\Order\Models\OrderItem;
use Illuminate\Support\Str;

class OrderItemObserver
{
    use LogAttemptDeleteResource;

    public function __construct(
        private readonly GetPaidQuantityAction $getPaidQuantityAction,
        private readonly CalculateOrderItemTotalPriceAction $calculateOrderItemTotalPriceAction
    ) {}

    public function creating(OrderItem $orderItem): void
    {
        $orderItem->sku_code = $orderItem->sku->code;
        $orderItem->name = $orderItem->sku->product->name;

        $orderItem->paid_quantity = $this->getPaidQuantityAction
            ->execute($orderItem->quantity, (float) $orderItem->minimum);

        $orderItem->total_price = moneyAmountToFloat($this->calculateOrderItemTotalPriceAction
            ->execute(ItemWithMinMaxData::fromOrderItem($orderItem)));

        $orderItem->discount_price = 0;

        $orderItem->price = $orderItem->sku->price;
        $orderItem->minimum = $orderItem->sku->minimum;
        $orderItem->maximum = $orderItem->sku->maximum;

        if ($orderItem->sku->product->description !== null) {
            $orderItem->description = (string) Str::of($orderItem->sku->product->description)->stripTags();
        }
    }
}
