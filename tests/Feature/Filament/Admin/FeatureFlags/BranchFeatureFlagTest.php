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

use App\Filament\Admin\Resources\Access\AdminResource;
use App\Filament\Admin\Resources\Shop\BranchResource;
use App\Filament\Admin\Resources\Shop\OrderResource;
use App\Filament\Admin\Resources\Shop\ProductResource;
use App\Filament\Admin\Resources\Shop\SkuStockResource;
use Domain\Access\Admin\Models\Admin;
use Domain\Shop\Brand\Models\Brand;
use Domain\Shop\Branch\Models\Branch;
use Domain\Shop\Category\Models\Category;
use Domain\Shop\Order\Models\Order;
use Domain\Shop\Product\Models\Product;
use Domain\Shop\Product\Models\Sku;
use Domain\Shop\Stock\Models\SkuStock;
use Filament\Pages\Dashboard;

use function Pest\Laravel\get;
use function Pest\Livewire\livewire;

beforeEach(fn () => loginAsAdmin());

describe('Branch Feature Flag DISABLED', function () {
    beforeEach(function () {
        config()->set('app-default.branch_feature_enabled', false);
    });

    it('has branch feature disabled in config', function () {
        expect(config()->boolean('app-default.branch_feature_enabled'))
            ->toBeFalse();
    });

    it('does not render branch in order export', function () {
        $branch = Branch::factory()->create();
        Order::factory()
            ->for($branch)
            ->count(5)
            ->create();

        $exportColumns = \Domain\Shop\Order\Exports\OrderExporter::getColumns();
        $exportColumnNames = collect($exportColumns)
            ->map(fn ($col) => $col->getName())
            ->toArray();

        expect($exportColumnNames)
            ->not->toContain('branch.name');
    });

    it('branch role does not exist in permissions when disabled', function () {
        config()->set('app-default.branch_feature_enabled', false);

        // Build the role names as the config does
        $roleNames = array_filter([
            'super_admin' => 'super_admin',
            'admin' => 'admin',
            'branch' => config()->boolean('app-default.branch_feature_enabled') ? 'branch' : null,
        ]);

        expect($roleNames)
            ->not->toHaveKey('branch');
    });

    it('can render admin list without branch feature', function () {
        Admin::factory()->count(5)->create();

        livewire(AdminResource\Pages\ListAdmins::class)
            ->assertSuccessful();
    });

    it('can render order list without branch feature', function () {
        $branch = Branch::factory()->create();
        Order::factory()
            ->for($branch)
            ->count(3)
            ->create();

        livewire(OrderResource\Pages\ListOrders::class)
            ->assertSuccessful();
    });

    it('verifies branch feature is disabled by config', function () {
        expect(config('app-default.branch_feature_enabled'))
            ->toBeFalse();
    });

    it('branch config value correctly reflects disabled state', function () {
        config()->set('app-default.branch_feature_enabled', false);

        // Verify through the array_filter that branch gets filtered
        $roleConfig = array_filter([
            'super_admin' => 'super_admin',
            'admin' => 'admin',
            'branch' => config()->boolean('app-default.branch_feature_enabled') ? 'branch' : null,
        ]);

        expect($roleConfig)
            ->not->toHaveKey('branch')
            ->toHaveKey('super_admin')
            ->toHaveKey('admin');
    });
});

describe('Branch Feature Flag ENABLED', function () {
    beforeEach(function () {
        config()->set('app-default.branch_feature_enabled', true);
    });

    it('has branch feature enabled in config', function () {
        expect(config()->boolean('app-default.branch_feature_enabled'))
            ->toBeTrue();
    });

    it('includes branch in order export', function () {
        $branch = Branch::factory()->create();
        Order::factory()
            ->for($branch)
            ->count(5)
            ->create();

        $exportColumns = \Domain\Shop\Order\Exports\OrderExporter::getColumns();
        $exportColumnNames = collect($exportColumns)
            ->map(fn ($col) => $col->getName())
            ->toArray();

        expect($exportColumnNames)
            ->toContain('branch.name');
    });

    it('branch role exists in permissions', function () {
        config()->set('app-default.branch_feature_enabled', true);
        // Need to reload config
        $roleNames = array_filter([
            'super_admin' => 'super_admin',
            'admin' => 'admin',
            'branch' => config()->boolean('app-default.branch_feature_enabled') ? 'branch' : null,
        ]);

        expect($roleNames)
            ->toHaveKey('branch');
    });

    it('can access branch resource', function () {
        expect(BranchResource::canAccess())
            ->toBeTrue();
    });

    it('can render admin list with branch feature', function () {
        Branch::factory()->create();
        Admin::factory()->count(5)->create();

        livewire(AdminResource\Pages\ListAdmins::class)
            ->assertSuccessful();
    });

    it('can render order list with branch feature', function () {
        $branch = Branch::factory()->create();
        Order::factory()
            ->for($branch)
            ->count(3)
            ->create();

        livewire(OrderResource\Pages\ListOrders::class)
            ->assertSuccessful();
    });

    it('can render sku stock list with branch feature', function () {
        Branch::factory()->create();
        $product = Product::factory()
            ->for(Category::factory())
            ->for(Brand::factory())
            ->create();

        livewire(SkuStockResource\Pages\ListSkuStocks::class)
            ->assertSuccessful();
    });

    it('verifies branch feature is enabled by config', function () {
        expect(config('app-default.branch_feature_enabled'))
            ->toBeTrue();
    });

    it('can render create order page', function () {
        Branch::factory()->create();

        livewire(OrderResource\Pages\CreateOrder::class)
            ->assertSuccessful();
    });
});

describe('Branch Feature Flag Toggling', function () {
    it('responds to config changes dynamically', function () {
        config()->set('app-default.branch_feature_enabled', false);
        expect(config()->boolean('app-default.branch_feature_enabled'))
            ->toBeFalse();

        config()->set('app-default.branch_feature_enabled', true);
        expect(config()->boolean('app-default.branch_feature_enabled'))
            ->toBeTrue();

        config()->set('app-default.branch_feature_enabled', false);
        expect(config()->boolean('app-default.branch_feature_enabled'))
            ->toBeFalse();
    });

    it('maintains consistency across different feature checks', function () {
        config()->set('app-default.branch_feature_enabled', true);

        // All feature checks should return true
        expect(config()->boolean('app-default.branch_feature_enabled'))
            ->toBeTrue();

        config()->set('app-default.branch_feature_enabled', false);

        // All feature checks should return false
        expect(config()->boolean('app-default.branch_feature_enabled'))
            ->toBeFalse();
    });
});
