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

use Domain\Settings\Actions\GetSettingAction;
use Domain\Settings\Actions\ToggleFeatureAction;
use Domain\Settings\Models\SettingCategory;
use Domain\Settings\Models\SettingFeature;
use Domain\Settings\Models\SettingValue;
use Domain\Shop\Branch\Models\Branch;

it('can toggle a feature', function () {
    $feature = SettingFeature::factory()->create([
        'enabled' => false,
    ]);

    $action = new ToggleFeatureAction();
    $result = $action->execute($feature, true);

    expect($result->enabled)->toBeTrue();
    expect(SettingFeature::find($feature->uuid)->enabled)->toBeTrue();
});

it('can override setting per branch', function () {
    $category = SettingCategory::create([
        'slug' => 'operations_test',
        'name' => 'Operations Test',
        'description' => 'Operations features',
    ]);

    $feature = SettingFeature::create([
        'key' => 'feature_inventory_test',
        'name' => 'Inventory Management Test',
        'description' => 'Test inventory feature',
        'setting_category_uuid' => $category->uuid,
        'enabled' => true,
        'default_value' => ['threshold' => 10],
    ]);

    $branch = Branch::factory()->create();

    SettingValue::create([
        'setting_feature_uuid' => $feature->uuid,
        'branch_uuid' => $branch->uuid,
        'value' => ['threshold' => 20],
    ]);

    expect(SettingValue::where('setting_feature_uuid', $feature->uuid)
        ->where('branch_uuid', $branch->uuid)
        ->exists())->toBeTrue();
});

it('get setting respects branch override', function () {
    $category = SettingCategory::create([
        'slug' => 'test_branch',
        'name' => 'Test Branch',
    ]);

    $feature = SettingFeature::create([
        'key' => 'test_setting_branch',
        'name' => 'Test Setting',
        'setting_category_uuid' => $category->uuid,
        'enabled' => true,
        'default_value' => ['value' => 'default'],
    ]);

    $branch = Branch::factory()->create();

    SettingValue::create([
        'setting_feature_uuid' => $feature->uuid,
        'branch_uuid' => $branch->uuid,
        'value' => ['value' => 'branch_override'],
    ]);

    $action = new GetSettingAction();

    // Get global value
    $globalValue = $action->execute('test_setting_branch');
    expect($globalValue)->toEqual(['value' => 'default']);

    // Get branch-specific value
    $branchValue = $action->execute('test_setting_branch', $branch->uuid);
    expect($branchValue)->toEqual(['value' => 'branch_override']);
});

it('feature persists across requests', function () {
    $category = SettingCategory::create([
        'slug' => 'test_persistence_cat',
        'name' => 'Test Persistence',
    ]);

    $feature = SettingFeature::create([
        'key' => 'persistence_test_feature',
        'name' => 'Persistence Test',
        'setting_category_uuid' => $category->uuid,
        'enabled' => false,
    ]);

    expect($feature->enabled)->toBeFalse();

    $action = new ToggleFeatureAction();
    $action->execute($feature, true);

    expect(SettingFeature::find($feature->uuid)->enabled)->toBeTrue();
});

it('setting category relationship works', function () {
    $category = SettingCategory::create([
        'slug' => 'test_relationship_cat',
        'name' => 'Test Relationship',
        'description' => 'Test category relationship',
    ]);

    $feature = SettingFeature::create([
        'key' => 'relationship_test_feature',
        'name' => 'Relationship Test',
        'setting_category_uuid' => $category->uuid,
        'enabled' => true,
    ]);

    expect($feature->category->uuid)->toBe($category->uuid);
    expect($category->settingFeatures->pluck('uuid'))->toContain($feature->uuid);
});

it('setting features are soft deleted', function () {
    $feature = SettingFeature::factory()->create();

    $feature->delete();

    expect(SettingFeature::withTrashed()->find($feature->uuid))->not->toBeNull();
    expect(SettingFeature::find($feature->uuid))->toBeNull();
});

it('cannot create duplicate feature key', function () {
    SettingFeature::create([
        'key' => 'duplicate_key_test',
        'name' => 'Feature 1',
        'enabled' => false,
    ]);

    try {
        SettingFeature::create([
            'key' => 'duplicate_key_test',
            'name' => 'Feature 2',
            'enabled' => false,
        ]);
        expect(true)->toBeFalse(); // Should not reach here
    } catch (\Illuminate\Database\QueryException $e) {
        expect(true)->toBeTrue();
    }
});
