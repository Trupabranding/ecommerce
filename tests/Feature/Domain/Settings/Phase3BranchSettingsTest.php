<?php

/*
 * Tests for Phase 3 - Multi-Branch Settings
 */

declare(strict_types=1);

use Domain\Settings\Actions\GetSettingAction;
use Domain\Settings\Queries\GetEffectiveSettingQuery;
use Domain\Settings\Models\SettingCategory;
use Domain\Settings\Models\SettingFeature;
use Domain\Settings\Models\SettingValue;
use Domain\Shop\Branch\Models\Branch;

describe('Phase 3: Multi-Branch Settings', function () {
    describe('Effective Setting Resolution', function () {
        it('returns feature default when no override exists', function () {
            $category = SettingCategory::create([
                'slug' => 'test_effective_default',
                'name' => 'Test Effective Default',
            ]);

            $feature = SettingFeature::create([
                'key' => 'feature_effective_default',
                'name' => 'Effective Default',
                'setting_category_uuid' => $category->uuid,
                'enabled' => true,
                'default_value' => ['value' => 'default'],
            ]);

            $branch = Branch::factory()->create();
            $query = new GetEffectiveSettingQuery();

            $result = $query->execute('feature_effective_default', $branch->uuid);

            expect($result)->toBe(['value' => 'default']);
        });

        it('returns branch override when it exists', function () {
            $category = SettingCategory::create([
                'slug' => 'test_effective_override',
                'name' => 'Test Effective Override',
            ]);

            $feature = SettingFeature::create([
                'key' => 'feature_effective_override',
                'name' => 'Effective Override',
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

            $query = new GetEffectiveSettingQuery();
            $result = $query->execute('feature_effective_override', $branch->uuid);

            expect($result)->toBe(['value' => 'branch_override']);
        });

        it('returns null when feature not found', function () {
            $query = new GetEffectiveSettingQuery();
            $result = $query->execute('nonexistent_feature');

            expect($result)->toBeNull();
        });

        it('differentiates between branch overrides and global defaults', function () {
            $category = SettingCategory::create([
                'slug' => 'test_differentiate',
                'name' => 'Test Differentiate',
            ]);

            $feature = SettingFeature::create([
                'key' => 'feature_differentiate',
                'name' => 'Differentiate',
                'setting_category_uuid' => $category->uuid,
                'enabled' => true,
                'default_value' => ['level' => 'basic'],
            ]);

            $branch1 = Branch::factory()->create();
            $branch2 = Branch::factory()->create();

            SettingValue::create([
                'setting_feature_uuid' => $feature->uuid,
                'branch_uuid' => $branch1->uuid,
                'value' => ['level' => 'advanced'],
            ]);

            $query = new GetEffectiveSettingQuery();

            // Branch 1 gets override
            expect($query->execute('feature_differentiate', $branch1->uuid))
                ->toBe(['level' => 'advanced']);

            // Branch 2 gets default
            expect($query->execute('feature_differentiate', $branch2->uuid))
                ->toBe(['level' => 'basic']);

            // Global gets default
            expect($query->execute('feature_differentiate'))
                ->toBe(['level' => 'basic']);
        });

        it('can check if branch has override', function () {
            $category = SettingCategory::create([
                'slug' => 'test_check_override',
                'name' => 'Test Check Override',
            ]);

            $feature = SettingFeature::create([
                'key' => 'feature_check_override',
                'name' => 'Check Override',
                'setting_category_uuid' => $category->uuid,
                'enabled' => true,
            ]);

            $branch = Branch::factory()->create();

            SettingValue::create([
                'setting_feature_uuid' => $feature->uuid,
                'branch_uuid' => $branch->uuid,
                'value' => ['custom' => true],
            ]);

            $query = new GetEffectiveSettingQuery();

            expect($query->hasBranchOverride('feature_check_override', $branch->uuid))
                ->toBeTrue();

            $otherBranch = Branch::factory()->create();
            expect($query->hasBranchOverride('feature_check_override', $otherBranch->uuid))
                ->toBeFalse();
        });
    });

    describe('GetSettingAction with Branch Context', function () {
        it('respects branch override in GetSettingAction', function () {
            $category = SettingCategory::create([
                'slug' => 'test_action_override',
                'name' => 'Test Action Override',
            ]);

            $feature = SettingFeature::create([
                'key' => 'feature_action_override',
                'name' => 'Action Override',
                'setting_category_uuid' => $category->uuid,
                'enabled' => true,
                'default_value' => ['threshold' => 10],
            ]);

            $branch = Branch::factory()->create();

            SettingValue::create([
                'setting_feature_uuid' => $feature->uuid,
                'branch_uuid' => $branch->uuid,
                'value' => ['threshold' => 25],
            ]);

            $action = new GetSettingAction();

            // Global setting
            $globalValue = $action->execute('feature_action_override');
            expect($globalValue)->toBe(['threshold' => 10]);

            // Branch override
            $branchValue = $action->execute('feature_action_override', $branch->uuid);
            expect($branchValue)->toBe(['threshold' => 25]);
        });

        it('caches branch-specific settings separately', function () {
            $category = SettingCategory::create([
                'slug' => 'test_branch_cache',
                'name' => 'Test Branch Cache',
            ]);

            $feature = SettingFeature::create([
                'key' => 'feature_branch_cache',
                'name' => 'Branch Cache',
                'setting_category_uuid' => $category->uuid,
                'enabled' => true,
                'default_value' => ['version' => 1],
            ]);

            $branch1 = Branch::factory()->create();
            $branch2 = Branch::factory()->create();

            SettingValue::create([
                'setting_feature_uuid' => $feature->uuid,
                'branch_uuid' => $branch1->uuid,
                'value' => ['version' => 2],
            ]);

            SettingValue::create([
                'setting_feature_uuid' => $feature->uuid,
                'branch_uuid' => $branch2->uuid,
                'value' => ['version' => 3],
            ]);

            $action = new GetSettingAction();

            // Each should get their own cached value
            expect($action->execute('feature_branch_cache', $branch1->uuid))
                ->toBe(['version' => 2]);

            expect($action->execute('feature_branch_cache', $branch2->uuid))
                ->toBe(['version' => 3]);
        });
    });

    describe('Branch-Specific Overrides Persistence', function () {
        it('persists branch-specific values', function () {
            $category = SettingCategory::create([
                'slug' => 'test_persist_branch',
                'name' => 'Test Persist Branch',
            ]);

            $feature = SettingFeature::create([
                'key' => 'feature_persist_branch',
                'name' => 'Persist Branch',
                'setting_category_uuid' => $category->uuid,
                'enabled' => true,
                'default_value' => ['level' => 'basic'],
            ]);

            $branch = Branch::factory()->create();

            SettingValue::create([
                'setting_feature_uuid' => $feature->uuid,
                'branch_uuid' => $branch->uuid,
                'value' => ['level' => 'premium'],
            ]);

            $persisted = SettingValue::where('setting_feature_uuid', $feature->uuid)
                ->where('branch_uuid', $branch->uuid)
                ->first();

            expect($persisted)->not->toBeNull();
            expect($persisted->value)->toBe(['level' => 'premium']);
        });

        it('enforces unique constraint on feature-branch combination', function () {
            $category = SettingCategory::create([
                'slug' => 'test_unique_constraint',
                'name' => 'Test Unique Constraint',
            ]);

            $feature = SettingFeature::create([
                'key' => 'feature_unique_constraint',
                'name' => 'Unique Constraint',
                'setting_category_uuid' => $category->uuid,
                'enabled' => true,
            ]);

            $branch = Branch::factory()->create();

            SettingValue::create([
                'setting_feature_uuid' => $feature->uuid,
                'branch_uuid' => $branch->uuid,
                'value' => ['data' => '1'],
            ]);

            try {
                SettingValue::create([
                    'setting_feature_uuid' => $feature->uuid,
                    'branch_uuid' => $branch->uuid,
                    'value' => ['data' => '2'],
                ]);
                expect(true)->toBeFalse(); // Should not reach here
            } catch (\Illuminate\Database\QueryException) {
                expect(true)->toBeTrue();
            }
        });

        it('can have multiple overrides for different branches', function () {
            $category = SettingCategory::create([
                'slug' => 'test_multiple_branches',
                'name' => 'Test Multiple Branches',
            ]);

            $feature = SettingFeature::create([
                'key' => 'feature_multiple_branches',
                'name' => 'Multiple Branches',
                'setting_category_uuid' => $category->uuid,
                'enabled' => true,
            ]);

            $branch1 = Branch::factory()->create();
            $branch2 = Branch::factory()->create();
            $branch3 = Branch::factory()->create();

            SettingValue::create([
                'setting_feature_uuid' => $feature->uuid,
                'branch_uuid' => $branch1->uuid,
                'value' => ['value' => 'override1'],
            ]);

            SettingValue::create([
                'setting_feature_uuid' => $feature->uuid,
                'branch_uuid' => $branch2->uuid,
                'value' => ['value' => 'override2'],
            ]);

            SettingValue::create([
                'setting_feature_uuid' => $feature->uuid,
                'branch_uuid' => $branch3->uuid,
                'value' => ['value' => 'override3'],
            ]);

            $count = SettingValue::where('setting_feature_uuid', $feature->uuid)->count();

            expect($count)->toBe(3);
        });
    });
});
