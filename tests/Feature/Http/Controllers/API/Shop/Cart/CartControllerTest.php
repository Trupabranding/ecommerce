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
use App\Http\Controllers\API\Shop\Cart\CartController;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\RequestFactories\CartRequestFactory;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseEmpty;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

/**
 * @property Branch $branch
 * @property \Domain\Access\Customer\Models\Customer $customer
 */

beforeEach(function () {
    $this->branch = Branch::factory()->enabled()->createOne();
    $this->customer = loginAsCustomer();
});

it('get list', function () {
    $product = createProduct($this->branch, 2);

    /** @var \Domain\Shop\Product\Models\Sku $sku */
    $sku = $product->skus[0];

    /** @var \Domain\Shop\Cart\Models\Cart $cart */
    $cart = Cart::factory()
        ->withQuantity(2)
        ->withSku($sku)
        ->withBranch($this->branch)
        ->withCustomer($this->customer)
        ->createOne();

    getJson('api/branches/'.$this->branch->getRouteKey().'/carts?include=sku.product')
        ->assertOk()
        ->assertJson(function (AssertableJson $json) use ($cart) {
            $json
                ->count('data', 1)
                ->where('data.0.type', 'carts')
                ->where('data.0.id', (string) $cart->getRouteKey())
                ->where('data.0.attributes.product_name', $cart->product_name)
                ->where('data.0.attributes.sku_code', $cart->sku_code)
                ->where('data.0.attributes.price', moneyJsonApi($cart->price))
                ->where('data.0.attributes.quantity', 2)
                ->etc();
        });

});

it('store', function () {

    $product = createProduct($this->branch, 2);

    $data = CartRequestFactory::new()
        ->withSku($product->skus[0])
        ->withQuantity(2)
        ->create();

    postJson('api/branches/'.$this->branch->getRouteKey().'/carts?include=sku.product', $data)
        ->assertValid()
        ->assertCreated();

});

it('update', function () {

    $product = createProduct($this->branch, 3);

    $cart = Cart::factory()
        ->withQuantity(2)
        ->withSku($product->skus[0])
        ->withBranch($this->branch)
        ->withCustomer($this->customer)
        ->createOne();

    assertDatabaseCount(Cart::class, 1);
    assertDatabaseHas(Cart::class, [
        'quantity' => 2,
    ]);

    putJson('api/branches/'.$this->branch->getRouteKey().'/carts/'.$cart->getRouteKey(), ['quantity' => 3])
        ->assertValid()
        ->assertOk();

    assertDatabaseCount(Cart::class, 1);
    assertDatabaseHas(Cart::class, [
        'quantity' => 3,
    ]);
});

it('delete', function () {

    $product = createProduct($this->branch, 2);

    $cart = Cart::factory()
        ->withQuantity(2)
        ->withSku($product->skus[0])
        ->withBranch($this->branch)
        ->withCustomer($this->customer)
        ->createOne();

    assertDatabaseCount(Cart::class, 1);

    deleteJson('api/branches/'.$this->branch->getRouteKey().'/carts/'.$cart->getRouteKey())
        ->assertNoContent();

    assertDatabaseEmpty(Cart::class);
});

it('empty only clears carts for the active branch', function () {
    $otherBranch = Branch::factory()->enabled()->createOne();

    $branchProduct = createProduct($this->branch, 2);
    $otherBranchProduct = createProduct($otherBranch, 2);

    $branchCart = Cart::factory()
        ->withQuantity(2)
        ->withSku($branchProduct->skus[0])
        ->withBranch($this->branch)
        ->withCustomer($this->customer)
        ->createOne();

    $otherBranchCart = Cart::factory()
        ->withQuantity(2)
        ->withSku($otherBranchProduct->skus[0])
        ->withBranch($otherBranch)
        ->withCustomer($this->customer)
        ->createOne();

    assertDatabaseCount(Cart::class, 2);

    app(CartController::class)->empty($this->branch);

    assertDatabaseCount(Cart::class, 1);
    assertDatabaseHas(Cart::class, [
        'uuid' => $otherBranchCart->getKey(),
        'branch_uuid' => $otherBranch->getKey(),
    ]);
    assertDatabaseMissing(Cart::class, [
        'uuid' => $branchCart->getKey(),
    ]);
});
