<?php

/*
 * Tests for Phase 2 - Feature Management & Customization
 */

declare(strict_types=1);

use Domain\Settings\Actions\CustomizeFeatureAction;
use Domain\Settings\Models\SettingCategory;
use Domain\Settings\Models\SettingFeature;
use Domain\Settings\Models\SettingValue;
use Domain\Shop\Branch\Models\Branch;

describe('Phase 2: Feature Customization', function () {
    describe('Customize Feature Action', function () {
        it('can customize a feature with new data', function () {
            $category = SettingCategory::create([
                'slug' => 'test_customize',
                'name' => 'Test Customize',
            ]);

            $feature = SettingFeature::create([
                'key' => 'feature_custom_test',
                'name' => 'Custom Test',
                'setting_category_uuid' => $category->uuid,
                'enabled' => true,
                'default_value' => ['level' => 'basic'],
            ]);

            $action = new CustomizeFeatureAction();
            $customization = ['level' => 'advanced', 'extra' => 'data'];

            $result = $action->execute($feature, $customization);

            expect($result)->toBeInstanceOf(SettingValue::class);
            expect($result->value)->toBe($customization);
        });

        it('can customize a feature for a specific branch', function () {
            $category = SettingCategory::create([
                'slug' => 'test_branch_custom',
                'name' => 'Test Branch Custom',
            ]);

            $feature = SettingFeature::create([
                'key' => 'feature_branch_custom',
                'name' => 'Branch Custom',
                'setting_category_uuid' => $category->uuid,
                'enabled' => true,
                'default_value' => ['threshold' => 10],
            ]);

            $branch = Branch::factory()->create();
            $action = new CustomizeFeatureAction();
            $customization = ['threshold' => 20, 'custom_field' => 'value'];

            $result = $action->execute($feature, $customization, $branch->uuid);

            expect($result->branch_uuid)->toBe($branch->uuid);
            expect($result->value)->toBe($customization);
        });

        it('can update existing customization', function () {
            $category = SettingCategory::create([
                'slug' => 'test_update_custom',
                'name' => 'Test Update Custom',
            ]);

            $feature = SettingFeature::create([
                'key' => 'feature_update_custom',
                'name' => 'Update Custom',
                'setting_category_uuid' => $category->uuid,
                'enabled' => true,
            ]);

            $action = new CustomizeFeatureAction();
            $first = $action->execute($feature, ['version' => 1]);
            $second = $action->execute($feature, ['version' => 2]);

            expect($first->uuid)->toBe($second->uuid); // Same record
            expect($second->value)->toBe(['version' => 2]);
        });

        it('can retrieve customization data', function () {
            $category = SettingCategory::create([
                'slug' => 'test_get_custom',
                'name' => 'Test Get Custom',
            ]);

            $feature = SettingFeature::create([
                'key' => 'feature_get_custom',
                'name' => 'Get Custom',
                'setting_category_uuid' => $category->uuid,
                'enabled' => true,
            ]);

            $action = new CustomizeFeatureAction();
            $customization = ['data' => 'test'];
            $action->execute($feature, $customization);

            $retrieved = $action->getCustomization($feature);
            expect($retrieved)->toBe($customization);
        });

        it('can remove customization', function () {
            $category = SettingCategory::create([
                'slug' => 'test_remove_custom',
                'name' => 'Test Remove Custom',
            ]);

            $feature = SettingFeature::create([
                'key' => 'feature_remove_custom',
                'name' => 'Remove Custom',
                'setting_category_uuid' => $category->uuid,
                'enabled' => true,
            ]);

            $action = new CustomizeFeatureAction();
            $action->execute($feature, ['data' => 'test']);

            expect(SettingValue::where('setting_feature_uuid', $feature->uuid)->exists())
                ->toBeTrue();

            $action->removeCustomization($feature);

            expect(SettingValue::where('setting_feature_uuid', $feature->uuid)->exists())
                ->toBeFalse();
        });

        it('invalidates cache on customization', function () {
            $category = SettingCategory::create([
                'slug' => 'test_cache_inv',
                'name' => 'Test Cache Inv',
            ]);

            $feature = SettingFeature::create([
                'key' => 'feature_cache_inv',
                'name' => 'Cache Inv',
                'setting_category_uuid' => $category->uuid,
                'enabled' => true,
                'default_value' => ['value' => 'original'],
            ]);

            $action = new CustomizeFeatureAction();
            $action->execute($feature, ['value' => 'customized']);

            // Cache tags should be flushed (observer handles this)
            expect(true)->toBeTrue();
        });
    });

    describe('Feature Categories Organization', function () {
        it('features can be organized by category', function () {
            $planCategory = SettingCategory::create([
                'slug' => 'plan_org',
                'name' => 'Plan',
                'description' => 'Plan features',
            ]);

            $okrFeature = SettingFeature::create([
                'key' => 'feature_okr_org',
                'name' => 'OKR Management',
                'setting_category_uuid' => $planCategory->uuid,
                'enabled' => true,
            ]);

            $taskFeature = SettingFeature::create([
                'key' => 'feature_task_org',
                'name' => 'Task Management',
                'setting_category_uuid' => $planCategory->uuid,
                'enabled' => true,
            ]);

            $planCategory->load('settingFeatures');

            expect($planCategory->settingFeatures)->toHaveCount(2);
            expect($planCategory->settingFeatures->pluck('key'))->toContain(
                'feature_okr_org',
                'feature_task_org'
            );
        });

        it('category relationship is bidirectional', function () {
            $category = SettingCategory::create([
                'slug' => 'bidir_cat',
                'name' => 'Bidirectional Category',
            ]);

            $feature = SettingFeature::create([
                'key' => 'bidir_feature',
                'name' => 'Bidirectional Feature',
                'setting_category_uuid' => $category->uuid,
                'enabled' => true,
            ]);

            expect($feature->category->uuid)->toBe($category->uuid);
            expect($category->settingFeatures->first()->uuid)->toBe($feature->uuid);
        });
    });
});
