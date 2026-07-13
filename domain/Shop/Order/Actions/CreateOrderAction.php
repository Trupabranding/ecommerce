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

use Domain\Shop\Cart\Models\Cart;
use Domain\Shop\Order\DataTransferObjects\OrderData;
use Domain\Shop\Order\Enums\OrderPaymentStatus;
use Domain\Shop\Order\Enums\OrderStatus;
use Domain\Shop\Order\Models\Order;
use Illuminate\Database\Eloquent\Relations\HasMany;

final readonly class CreateOrderAction
{
    public function __construct(
        private OrderCreatedPipelineAction $orderCreatedPipelineAction,
    ) {}

    public function execute(OrderData $data): Order
    {
        $data->customer->load([
            'carts' => fn (HasMany $query) => $query
                ->whereBelongsTo($data->branch),
        ]);

        if ($data->customer->carts->isEmpty()) {
            abort(400, trans('Cart is empty.'));
        }

        $order = Order::create([
            'branch_uuid' => $data->branch->getKey(),
            'customer_uuid' => $data->customer->getKey(),
            'notes' => $data->notes,
            'payment_status' => OrderPaymentStatus::pending,
            'payment_method' => $data->payment_method,
            'status' => OrderStatus::pending,
            'claim_type' => $data->claimType,
            'total_price' => 0,
            'delivery_price' => 0, // TODO: delivery price
            'claim_at' => $data->claim_at,
            'purchased_at' => now(),
        ]);

        $data->customer->carts->each(fn (Cart $cart) => $order->orderItems()->create([
            'sku_uuid' => $cart->sku->getKey(),
            'quantity' => $cart->quantity,
        ]));

        $this->orderCreatedPipelineAction->execute($order);

        return $order;
    }
}
