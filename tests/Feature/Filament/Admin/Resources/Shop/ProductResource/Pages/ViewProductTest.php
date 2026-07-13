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

use App\Filament\Admin\Resources\Shop\ProductResource;
use Domain\Shop\Brand\Models\Brand;
use Domain\Shop\Category\Models\Category;
use Domain\Shop\Product\Models\Product;

use function Pest\Laravel\get;

beforeEach(function () {
    loginAsAdmin();
});

it('can view product', function () {
    $product = Product::factory()
        ->for(Brand::factory())
        ->for(Category::factory())
        ->create();

    get(ProductResource::getUrl('view', ['record' => $product]))
        ->assertOk();
});

it('can view product with multiple skus', function () {
    $product = Product::factory()
        ->for(Brand::factory())
        ->for(Category::factory())
        ->has(\Domain\Shop\Product\Models\Sku::factory()->count(3))
        ->create();

    get(ProductResource::getUrl('view', ['record' => $product]))
        ->assertOk();
});

it('can view trashed product', function () {
    $product = Product::factory()
        ->for(Brand::factory())
        ->for(Category::factory())
        ->create();

    $product->delete();

    get(ProductResource::getUrl('view', ['record' => $product]))
        ->assertOk();
});
