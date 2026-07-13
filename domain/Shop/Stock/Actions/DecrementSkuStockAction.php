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

namespace Domain\Shop\Stock\Actions;

use Domain\Shop\Order\Models\Order;
use Domain\Shop\Order\Models\OrderItem;
use Domain\Shop\Stock\Enums\SkuStockType;
use Illuminate\Database\Eloquent\Relations\HasMany;

final readonly class DecrementSkuStockAction
{
    public function __construct(private NotifyLowerStockAction $notifyLowerStockAction) {}

    public function execute(Order $order): void
    {
        $order->load([
            'orderItems.sku.skuStocks' => fn (HasMany $query) => $query
                ->whereBelongsTo($order->branch),
        ]);

        $order->orderItems->map(fn (OrderItem $orderItem) => $this->decrement($orderItem));
    }

    private function decrement(OrderItem $orderItem): void
    {
        /** @var \Domain\Shop\Stock\Models\SkuStock $skuStock */
        $skuStock = $orderItem->sku->skuStocks[0];

        if ($skuStock->type === SkuStockType::base_on_stock) {

            $skuStock->decrement('count', $orderItem->quantity);

            if ($skuStock->count <= $skuStock->warning) {
                $this->notifyLowerStockAction->execute($skuStock, $orderItem->order);
            }
        }
    }
}
