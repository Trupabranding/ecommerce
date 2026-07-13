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

use Domain\Shop\Branch\Models\Branch;
use Domain\Shop\Cart\Models\Cart;
use Domain\Shop\Customer\Models\Customer;
use Domain\Shop\Order\Models\Order;
use Domain\Shop\Order\Models\OrderItem;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\RequestFactories\OrderRequestFactory;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseEmpty;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\postJson;

it('can submit order', function () {

    $branch = Branch::factory()
        ->enabled()
        ->createOne();

    $product = createProduct($branch, 5.1);

    $customer = Customer::factory()
        ->active()
        ->createOne();

    Cart::factory()
        ->withQuantity(5.1)
        ->withSku($product->skus[0])
        ->withBranch($branch)
        ->withCustomer($customer)
        ->createOne();

    $data = OrderRequestFactory::new()
        ->create();

    loginAsCustomer($customer);

    assertDatabaseEmpty(Order::class);
    assertDatabaseEmpty(OrderItem::class);

    postJson('api/branches/'.$branch->getRouteKey().'/orders?include=orderItems.sku', $data)
        ->assertValid()
        ->assertCreated()
        ->assertJson(function (AssertableJson $json) use ($customer) {
            $order = Order::first();
            $json
                ->where('data.type', 'orders')
                ->where('data.id', $order->uuid)
                ->where('data.attributes.receipt_number', $order->receipt_number)
                ->where('data.attributes.payment_status', $order->payment_status->getLabel())
                ->where('data.attributes.payment_method', $order->payment_method?->getLabel())
                ->where('data.attributes.status', $order->status->getLabel())
                ->where('data.attributes.total_price', moneyJsonApi($order->total_price))
                ->where(
                    'data.attributes.claim_at',
                    $order->claim_at
                        ->timezone($customer->timezone)->format('Y-m-d h:i A')
                )
                ->where(
                    'data.attributes.created_at',
                    $order->created_at
                        ->timezone($customer->timezone)->format('Y-m-d h:i A')
                )
                ->etc();
        });

    assertDatabaseCount(Order::class, 1);
    assertDatabaseCount(OrderItem::class, 1);

    assertDatabaseHas(Order::class, [
        'payment_method' => $data['payment_method'],
        'notes' => $data['notes'],
        'claim_at' => now()->parse($data['claim_at'])->timezone($customer->timezone),
        'claim_type' => $data['claim_type'],
    ]);
    assertDatabaseHas(OrderItem::class, [
        'order_uuid' => Order::value('uuid'),
        'quantity' => 5.1,
        'sku_uuid' => $product->skus[0]->uuid,
    ]);

});
