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

use App\Filament\Admin\Resources\Shop\CategoryResource;
use Domain\Shop\Category\Models\Category;

use function Pest\Laravel\get;
use function Pest\Livewire\livewire;

beforeEach(fn () => loginAsAdmin());

it('can render create with configuration', function () {
    get(CategoryResource::getUrl('create'))
        ->assertOk();
});

it('can save category configuration', function () {
    $category = Category::factory()->create([
        'configuration' => [
            'display_rules' => [
                'template' => 'minimal',
                'show_product_count' => false,
                'image_requirements' => [
                    'required_count' => 2,
                    'min_dimensions' => '500x500',
                ],
            ],
            'product_rules' => [
                'require_description' => false,
                'require_images' => 2,
                'required_attributes' => [],
                'custom_fields' => [],
            ],
            'pricing_rules' => [
                'tax_rate_override' => 5,
                'allow_bulk_discounts' => true,
                'min_price_threshold' => null,
            ],
            'inventory_rules' => [
                'track_by_sku' => false,
                'allow_backorders' => true,
                'low_stock_threshold_override' => null,
                'show_stock_status' => true,
            ],
            'seo_rules' => [
                'meta_title_template' => null,
                'meta_description_template' => null,
                'focus_keyword' => null,
            ],
        ],
    ]);

    expect($category)->not->toBeNull();
    expect($category->configuration)->not->toBeNull();
    expect($category->configuration['display_rules']['template'])->toBe('minimal');
    expect($category->configuration['display_rules']['show_product_count'])->toBeFalse();
    expect($category->configuration['pricing_rules']['tax_rate_override'])->toBe(5);
});

it('can retrieve configuration via model methods', function () {
    $category = Category::factory()->create([
        'configuration' => [
            'display_rules' => [
                'template' => 'detailed',
                'show_product_count' => false,
            ],
            'pricing_rules' => [
                'tax_rate_override' => 8.0,
            ],
        ],
    ]);

    $displayRules = $category->getDisplayRules();
    expect($displayRules['template'])->toBe('detailed');
    expect($displayRules['show_product_count'])->toBeFalse();

    $pricingRules = $category->getPricingRules();
    expect($pricingRules['tax_rate_override'])->toBe(8);
});

it('applies default configuration when none set', function () {
    $category = Category::factory()->create();

    $config = $category->getConfiguration();
    expect($config)->toHaveKey('display_rules');
    expect($config)->toHaveKey('product_rules');
    expect($config)->toHaveKey('pricing_rules');
    expect($config)->toHaveKey('inventory_rules');
    expect($config)->toHaveKey('seo_rules');

    expect($config['display_rules']['template'])->toBe('default');
    expect($config['product_rules']['require_description'])->toBeTrue();
    expect($config['inventory_rules']['track_by_sku'])->toBeTrue();
    expect($config['inventory_rules']['allow_backorders'])->toBeFalse();
});

it('can update category configuration', function () {
    $category = Category::factory()->create();

    $updatedConfig = [
        'display_rules' => [
            'template' => 'minimal',
            'show_product_count' => false,
            'image_requirements' => [
                'required_count' => 3,
                'min_dimensions' => '500x500',
            ],
        ],
        'product_rules' => [
            'require_description' => false,
            'require_images' => 2,
            'required_attributes' => [],
            'custom_fields' => [],
        ],
        'pricing_rules' => [
            'tax_rate_override' => 5.0,
            'allow_bulk_discounts' => true,
            'min_price_threshold' => null,
        ],
        'inventory_rules' => [
            'track_by_sku' => false,
            'allow_backorders' => true,
            'low_stock_threshold_override' => 10,
            'show_stock_status' => false,
        ],
        'seo_rules' => [
            'meta_title_template' => 'Buy {category_name}',
            'meta_description_template' => null,
            'focus_keyword' => 'products',
        ],
    ];

    $category->update(['configuration' => $updatedConfig]);
    $updated = $category->refresh();

    expect($updated->configuration['display_rules']['template'])->toBe('minimal');
    expect($updated->configuration['display_rules']['show_product_count'])->toBeFalse();
    expect($updated->configuration['display_rules']['image_requirements']['required_count'])->toBe(3);
    expect($updated->configuration['pricing_rules']['tax_rate_override'])->toBe(5);
});

it('can retrieve specific rule sets', function () {
    $category = Category::factory()->create();

    expect($category->getDisplayRules())->toBeArray();
    expect($category->getProductRules())->toBeArray();
    expect($category->getPricingRules())->toBeArray();
    expect($category->getInventoryRules())->toBeArray();
    expect($category->getSeoRules())->toBeArray();
});

it('preserves configuration structure through save cycles', function () {
    $category = Category::factory()->create([
        'configuration' => [
            'display_rules' => [
                'template' => 'detailed',
                'show_product_count' => true,
                'image_requirements' => [
                    'required_count' => 2,
                    'min_dimensions' => '600x600',
                ],
            ],
        ],
    ]);

    $config = $category->getConfiguration();
    $category->configuration = $config;
    $category->save();

    $refreshed = $category->refresh();
    expect($refreshed->configuration['display_rules']['template'])->toBe('detailed');
    expect($refreshed->configuration['display_rules']['image_requirements']['min_dimensions'])->toBe('600x600');
});
