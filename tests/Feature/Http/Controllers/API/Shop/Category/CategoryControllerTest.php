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

use Domain\Shop\Branch\Models\Branch;
use Domain\Shop\Brand\Models\Brand;
use Domain\Shop\Category\Models\Category;
use Domain\Shop\Product\Database\AttributeOptionForProductSku;
use Domain\Shop\Product\Enums\AttributeFieldType;
use Domain\Shop\Product\Models\Product;
use Domain\Shop\Product\Models\Sku;
use Domain\Shop\Stock\Models\SkuStock;

use function Pest\Laravel\assertDatabaseEmpty;
use function Pest\Laravel\getJson;

dataset(
    'includes',
    [
        'media',
        'parent',
        'parent.media',

        'products',
        'products.brand',
        'products.media',
        'products.skus',
    ]
);

it('list', function (?string $include) {

    assertDatabaseEmpty(Category::class);

    seedCategory();

    $response = getJson('api/categories?include='.$include)
        ->assertOk();

    expect($response)->toMatchSnapshot();
})
    ->with('includes');

it('show', function (?string $include) {

    assertDatabaseEmpty(Category::class);

    $category = seedCategory();

    $response = getJson('api/categories/'.$category->getRouteKey().'?include='.$include)
        ->assertOk();

    expect($response)->toMatchSnapshot();
})
    ->with('includes');

function seedCategory(): Category
{
    $category = Category::factory([
        'name' => 'test name',
        'description' => 'test description',
    ])
        ->for(
            Category::factory([
                'name' => 'test parent name',
                'description' => 'test parent description',
            ])
                ->hasSpecificMedia()
                ->isVisibleStatus(),
            'parent',
        )
        ->hasSpecificMedia()
        ->isVisibleStatus()
        ->createOne();

    $product = Product::factory([
        'parent_sku' => 'sku sample',
        'name' => 'Samsung Galaxy S21',
        'description' => 'sample description',
    ])
        ->for($category)
        ->inStockStatus()
        ->hasSpecificMedia()
        ->for(
            Brand::factory(['name' => 'test brand'])
                ->hasSpecificMedia()
        )->createOne();

    Sku::factory()->forProduct(
        product: $product,
        priceOrSkuFactory: Sku::factory([
            'code' => 'sample-code',
            'price' => 349,
            'minimum' => 1,
            'maximum' => 10,
        ])
            ->hasSpecificMedia()
            ->has(
                SkuStock::factory()
                    ->unlimited()
                    ->for(
                        Branch::factory()
                            ->enabled()
                            ->hasSpecificMedia()
                    )
            ),
        attributeOptions: [
            new AttributeOptionForProductSku(
                'Color',
                'Blue',
                attributeFieldType: AttributeFieldType::color_picker
            ),
        ],
    );

    return $category;
}
