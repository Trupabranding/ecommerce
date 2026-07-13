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

use App\Filament\Admin\Resources\Shop\BrandResource;
use Domain\Shop\Brand\Models\Brand;

use function Pest\Laravel\get;
use function Pest\Livewire\livewire;

beforeEach(fn () => loginAsAdmin());

it('can render create with configuration', function () {
    get(BrandResource::getUrl('create'))
        ->assertOk();
});

it('can save brand configuration', function () {
    $brand = Brand::factory()
        ->make();

    livewire(BrandResource\Pages\CreateBrand::class)
        ->fillForm([
            'name' => $brand->name,
            'configuration' => [
                'display_settings' => [
                    'brand_color' => '#FF5733',
                    'display_logo' => true,
                    'brand_story' => 'Our premium brand story',
                ],
                'pricing_rules' => [
                    'minimum_margin_percent' => 25,
                    'maximum_discount_percent' => 15,
                    'suggested_retail_markup' => 50,
                ],
                'requirements' => [
                    'requires_certification' => 'ISO',
                    'allow_variants' => true,
                    'quality_tier' => 'premium',
                ],
            ],
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $created = Brand::where('name', $brand->name)->first();
    expect($created)->not->toBeNull();
    expect($created->configuration)->not->toBeNull();
    expect($created->configuration['display_settings']['brand_color'])->toBe('#FF5733');
    expect($created->configuration['pricing_rules']['minimum_margin_percent'])->toBe(25);
    expect($created->configuration['requirements']['quality_tier'])->toBe('premium');
});

it('can retrieve configuration via model methods', function () {
    $brand = Brand::factory()->create([
        'configuration' => [
            'display_settings' => [
                'brand_color' => '#ABCDEF',
                'display_logo' => false,
            ],
            'pricing_rules' => [
                'minimum_margin_percent' => 30,
                'maximum_discount_percent' => 5,
            ],
        ],
    ]);

    $displaySettings = $brand->getDisplaySettings();
    expect($displaySettings['brand_color'])->toBe('#ABCDEF');
    expect($displaySettings['display_logo'])->toBeFalse();

    $pricingRules = $brand->getPricingRules();
    expect($pricingRules['minimum_margin_percent'])->toBe(30);
    expect($pricingRules['maximum_discount_percent'])->toBe(5);
});

it('applies default configuration when none set', function () {
    $brand = Brand::factory()->create();

    $config = $brand->getConfiguration();
    expect($config)->toHaveKey('display_settings');
    expect($config)->toHaveKey('pricing_rules');
    expect($config)->toHaveKey('requirements');

    expect($config['display_settings']['brand_color'])->toBe('#000000');
    expect($config['display_settings']['display_logo'])->toBeTrue();
    expect($config['pricing_rules']['minimum_margin_percent'])->toBe(20);
    expect($config['pricing_rules']['maximum_discount_percent'])->toBe(10);
    expect($config['requirements']['quality_tier'])->toBe('standard');
    expect($config['requirements']['allow_variants'])->toBeTrue();
});

it('can update brand configuration', function () {
    $brand = Brand::factory()->create();

    $updatedConfig = [
        'display_settings' => [
            'brand_color' => '#123456',
            'display_logo' => false,
            'brand_story' => 'Updated brand story',
        ],
        'pricing_rules' => [
            'minimum_margin_percent' => 28,
            'maximum_discount_percent' => 12,
            'suggested_retail_markup' => 45,
        ],
        'requirements' => [
            'requires_certification' => 'ISO',
            'allow_variants' => false,
            'quality_tier' => 'premium',
        ],
    ];

    $brand->update(['configuration' => $updatedConfig]);
    $updated = $brand->refresh();

    expect($updated->configuration['display_settings']['brand_color'])->toBe('#123456');
    expect($updated->configuration['display_settings']['display_logo'])->toBeFalse();
    expect($updated->configuration['display_settings']['brand_story'])->toBe('Updated brand story');
    expect($updated->configuration['pricing_rules']['minimum_margin_percent'])->toBe(28);
});

it('can retrieve specific rule sets', function () {
    $brand = Brand::factory()->create();

    expect($brand->getDisplaySettings())->toBeArray();
    expect($brand->getPricingRules())->toBeArray();
    expect($brand->getRequirements())->toBeArray();
});

it('enforces pricing rules in configuration', function () {
    $brand = Brand::factory()->create([
        'configuration' => [
            'pricing_rules' => [
                'minimum_margin_percent' => 35,
                'maximum_discount_percent' => 8,
            ],
        ],
    ]);

    $rules = $brand->getPricingRules();
    expect($rules['minimum_margin_percent'])->toBeGreaterThanOrEqual(20);
    expect($rules['maximum_discount_percent'])->toBeLessThanOrEqual(25);
});

it('honors quality tier requirements', function () {
    $brand = Brand::factory()->create([
        'configuration' => [
            'requirements' => [
                'quality_tier' => 'luxury',
                'allow_variants' => false,
            ],
        ],
    ]);

    $requirements = $brand->getRequirements();
    expect($requirements['quality_tier'])->toBe('luxury');
    expect($requirements['allow_variants'])->toBeFalse();
});

it('preserves configuration structure through save cycles', function () {
    $brand = Brand::factory()->create([
        'configuration' => [
            'display_settings' => [
                'brand_color' => '#FEDCBA',
                'display_logo' => true,
                'brand_story' => 'Premium luxury brand',
            ],
            'pricing_rules' => [
                'minimum_margin_percent' => 40,
                'maximum_discount_percent' => 5,
            ],
        ],
    ]);

    $config = $brand->getConfiguration();
    $brand->configuration = $config;
    $brand->save();

    $refreshed = $brand->refresh();
    expect($refreshed->configuration['display_settings']['brand_color'])->toBe('#FEDCBA');
    expect($refreshed->configuration['display_settings']['brand_story'])->toBe('Premium luxury brand');
    expect($refreshed->configuration['pricing_rules']['minimum_margin_percent'])->toBe(40);
});
