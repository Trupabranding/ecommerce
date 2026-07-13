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

namespace Database\Seeders;

use Domain\Shop\Branch\Models\Branch;
use Domain\Shop\Brand\Models\Brand;
use Domain\Shop\Category\Models\Category;
use Domain\Shop\Product\Database\AttributeOptionForProductSku;
use Domain\Shop\Product\Database\Factories\SkuFactory;
use Domain\Shop\Product\Enums\AttributeFieldType;
use Domain\Shop\Product\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * @throws \Exception
     */
    public function run(): void
    {
        /** @var \Domain\Shop\Category\Models\Category $category */
        $category = Category::whereChild()->first();
        [$iPhone, $samsung] = Brand::orderBy('name')->get();
        /** @var \Domain\Shop\Branch\Models\Branch[] $branches */
        $branches = Branch::orderBy('name')->get();

        $product = Product::factory(['name' => 'Samsung Galaxy S21'])
            ->for($category)
            ->for($samsung)
            ->inStockStatus()
            ->hasRandomMedia()
            ->createOne();

        SkuFactory::forProduct(
            product: $product,
            priceOrSkuFactory: 100,
            attributeOptions: [

                new AttributeOptionForProductSku(
                    'Color',
                    'Red',
                    attributeFieldType: AttributeFieldType::color_picker
                ),
                new AttributeOptionForProductSku(
                    'Ram',
                    '2',
                    attributeFieldSuffix: 'GB',
                    attributeFieldType: AttributeFieldType::numeric
                ),
                new AttributeOptionForProductSku(
                    'Storage',
                    '8',
                    attributeFieldSuffix: 'GB',
                    attributeFieldType: AttributeFieldType::numeric
                ),
            ],
            branches: $branches,
        );
        SkuFactory::forProduct(
            product: $product,
            priceOrSkuFactory: 200,
            attributeOptions: [
                new AttributeOptionForProductSku(
                    'Color',
                    'Black',
                    attributeFieldType: AttributeFieldType::color_picker
                ),
                new AttributeOptionForProductSku(
                    'Ram',
                    '4',
                    attributeFieldSuffix: 'GB',
                    attributeFieldType: AttributeFieldType::numeric
                ),
                new AttributeOptionForProductSku(
                    'Storage',
                    '1',
                    attributeFieldSuffix: 'GB',
                    attributeFieldType: AttributeFieldType::numeric
                ),
            ],
            branches: $branches,
        );
        SkuFactory::forProduct(
            product: $product,
            priceOrSkuFactory: 300,
            attributeOptions: [
                new AttributeOptionForProductSku(
                    'Color',
                    'White',
                    attributeFieldType: AttributeFieldType::color_picker
                ),
                new AttributeOptionForProductSku(
                    'Ram',
                    '16',
                    attributeFieldSuffix: 'GB',
                    attributeFieldType: AttributeFieldType::numeric
                ),
                new AttributeOptionForProductSku(
                    'Storage',
                    '64',
                    attributeFieldSuffix: 'GB',
                    attributeFieldType: AttributeFieldType::numeric
                ),
            ],
            branches: $branches,
        );

        $product = Product::factory(['name' => 'iPhone 14 MAX'])
            ->for($category)
            ->for($iPhone)
            ->inStockStatus()
            ->hasRandomMedia()
            ->createOne();

        SkuFactory::forProduct(
            product: $product,
            priceOrSkuFactory: 400,
            attributeOptions: [
                new AttributeOptionForProductSku(
                    'Color',
                    'Red',
                    attributeFieldType: AttributeFieldType::color_picker
                ),
                new AttributeOptionForProductSku(
                    'Ram',
                    '2',
                    attributeFieldSuffix: 'GB',
                    attributeFieldType: AttributeFieldType::numeric
                ),
                new AttributeOptionForProductSku(
                    'Storage',
                    '8',
                    attributeFieldSuffix: 'GB',
                    attributeFieldType: AttributeFieldType::numeric
                ),
            ],
            branches: $branches,
        );
        SkuFactory::forProduct(
            product: $product,
            priceOrSkuFactory: 500,
            attributeOptions: [
                new AttributeOptionForProductSku(
                    'Color',
                    'Black',
                    attributeFieldType: AttributeFieldType::color_picker
                ),
                new AttributeOptionForProductSku(
                    'Ram',
                    '4',
                    attributeFieldSuffix: 'GB',
                    attributeFieldType: AttributeFieldType::numeric
                ),
                new AttributeOptionForProductSku(
                    'Storage',
                    '1',
                    attributeFieldSuffix: 'GB',
                    attributeFieldType: AttributeFieldType::numeric
                ),
            ],
            branches: $branches,
        );
        SkuFactory::forProduct(
            product: $product,
            priceOrSkuFactory: 600,
            attributeOptions: [
                new AttributeOptionForProductSku(
                    'Color',
                    'White',
                    attributeFieldType: AttributeFieldType::color_picker
                ),
                new AttributeOptionForProductSku(
                    'Ram',
                    '16',
                    attributeFieldSuffix: 'GB',
                    attributeFieldType: AttributeFieldType::numeric
                ),
                new AttributeOptionForProductSku(
                    'Storage',
                    '64',
                    attributeFieldSuffix: 'GB',
                    attributeFieldType: AttributeFieldType::numeric
                ),
            ],
            branches: $branches,
        );
    }
}
