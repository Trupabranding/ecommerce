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

use Domain\Access\Admin\Models\Admin;
use Domain\Shop\Branch\Models\Branch;
use Domain\Shop\Brand\Models\Brand;
use Domain\Shop\Category\Models\Category;
use Domain\Shop\Customer\Models\Customer;
use Domain\Shop\Order\Actions\GenerateReceiptNumberAction;
use Domain\Shop\Product\Models\Attribute;
use Domain\Shop\Product\Models\AttributeOption;
use Domain\Shop\Product\Models\Product;
use Domain\Shop\Product\Models\Sku;
use Domain\Shop\Stock\Models\SkuStock;
use Laravel\Sanctum\Sanctum;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Config\PermissionConfig;
use Tests\Support\GenerateReceiptNumberActionFake;

use function Pest\Laravel\actingAs;

function loginAsAdmin(?Admin $admin = null): Admin
{
    $admin ??= createAdmin();

    $admin->assignRole(PermissionConfig::admin());

    actingAs($admin);

    return $admin;
}

function loginAsCustomer(?Customer $customer = null): Customer
{
    $customer ??= createCustomer();

    Sanctum::actingAs($customer);

    return $customer;
}

function createAdmin(): Admin
{
    return Admin::factory()
        ->createOne();
}

function createCustomer(): Customer
{
    return Customer::factory()
        ->active()
        ->createOne();
}

function createProduct(Branch $branch, float $stockCount): Product
{
    $product = Product::factory()
        ->for(Category::factory())
        ->for(Brand::factory())
        ->inStockStatus()
        ->hasRandomMedia()
        ->createOne();

    Sku::factory()->forProduct(
        product: $product,
        priceOrSkuFactory: Sku::factory([
            'price' => 123.45,
            'minimum' => 0,
            'maximum' => 0,
        ])
            ->has(
                SkuStock::factory()->baseOnStock($stockCount)
                    ->for($branch)
            )
            ->hasRandomMedia()
            ->regenerateCode(),
        attributeOptions: [
            AttributeOption::factory()
                ->for(Attribute::factory()),
        ]
    );

    return $product;
}

function fakeGenerateReceiptNumberActionFake(): void
{
    app()->bind(GenerateReceiptNumberAction::class, GenerateReceiptNumberActionFake::class);
}

function mockStrUuid(): void
{
    $counter = 0;
    Str::createUuidsUsing(function () use (&$counter) {
        $fakeUuids = require __DIR__.'/fakeUuids.php';

        $fake = $fakeUuids[$counter] ?? throw new \Exception(
            'Insufficient fake uuid, index used ['.$counter.'].'
        );
        $counter++;

        // Call to a member function toString() on string
        return Str::of($fake);
    });
}

function moneyJsonApi(float $amount): string
{
    return \Illuminate\Support\Number::currency($amount);
}
