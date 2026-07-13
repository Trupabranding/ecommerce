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
use function Pest\Livewire\livewire;

beforeEach(fn () => loginAsAdmin());

it('can render edit', function () {
    $parentCategory = Category::factory()->create();
    $category = Category::factory()
        ->for($parentCategory, 'parent')
        ->create();
    $product = Product::factory()
        ->for($category)
        ->for(Brand::factory())
        ->create();

    get(ProductResource::getUrl('edit', ['record' => $product]))
        ->assertOk();
});

it('can edit', function () {
    $parentCategory = Category::factory()->create();
    $category = Category::factory()
        ->for($parentCategory, 'parent')
        ->create();
    $product = Product::factory()
        ->for($category)
        ->for(Brand::factory())
        ->create();
    $newName = 'Updated Product Name';

    $product->update(['name' => $newName]);

    expect($product->fresh()->name)->toBe($newName);
});
