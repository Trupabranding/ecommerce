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

use Database\Factories\Shop\TagFactory;
use Domain\Shop\Branch\Models\Branch;
use Domain\Shop\Brand\Models\Brand;
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
        'brand',
        'brand.media',
        'skus',
        'skus.attributeOptions',
        'skus.attributeOptions.attribute',
        'skus.media',
        'skus.skuStocks',
        'category',
        'category.parent',
        'tags',
    ]
);

it('list', function (?string $include) {

    assertDatabaseEmpty(Product::class);

    seedProduct();

    $response = getJson('api/products?include='.$include)
        ->assertOk();

    expect($response)->toMatchSnapshot();
})
    ->with('includes');

it('show', function (?string $include) {

    assertDatabaseEmpty(Product::class);

    $product = seedProduct();

    $response = getJson('api/products/'.$product->getRouteKey().'?include='.$include)
        ->assertOk();

    expect($response)->toMatchSnapshot();
})
    ->with('includes');

function seedProduct(): Product
{
    $product = Product::factory([
        'parent_sku' => 'sku sample',
        'name' => 'Samsung Galaxy S21',
        'description' => 'sample description',
    ])
        ->has(
            TagFactory::new(['name' => 'test tag'])
        )
        ->inStockStatus()
        ->hasSpecificMedia()
        ->for(
            Brand::factory(['name' => 'test brand'])
                ->hasSpecificMedia()
        )
        ->createOne();

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

    return $product;
}
