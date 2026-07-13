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

use App\Filament\Admin\Resources\Shop\ProductResource;
use Domain\Shop\Brand\Models\Brand;
use Domain\Shop\Category\Models\Category;
use Domain\Shop\Product\Models\Product;

use function Pest\Laravel\get;
use function Pest\Livewire\livewire;

beforeEach(fn () => loginAsAdmin());

it('can render create', function () {
    get(ProductResource::getUrl('create'))
        ->assertOk();
});

it('can create', function () {
    $parentCategory = Category::factory()->create();
    $category = Category::factory()
        ->for($parentCategory, 'parent')
        ->create();
    $brand = Brand::factory()->create();
    $newProduct = Product::factory()
        ->for($category)
        ->for($brand)
        ->make();

    livewire(ProductResource\Pages\CreateProduct::class)
        ->fillForm([
            'name' => $newProduct->name,
            'parent_sku' => $newProduct->parent_sku,
            'description' => $newProduct->description,
            'category_uuid' => $category->uuid,
            'brand_uuid' => $brand->uuid,
            'status' => $newProduct->status,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Product::where('name', $newProduct->name)->exists())->toBeTrue();
});
