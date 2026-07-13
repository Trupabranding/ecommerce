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

namespace Domain\Shop\Branch\Observers;

use App\Filament\Resources\Caching\BranchCache;
use App\Observers\LogAttemptDeleteResource;
use Domain\Access\Admin\Models\Admin;
use Domain\Shop\Branch\Models\Branch;
use Domain\Shop\Order\Models\EloquentBuilder\OrderEloquentBuilder;
use Filament\Support\Exceptions\Halt;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Config\PermissionConfig;

class BranchObserver
{
    use LogAttemptDeleteResource;

    public function created(Branch $branch): void
    {
        Admin::role(PermissionConfig::superAdmin(), guard: 'admin')
            ->get()
            ->each(fn (Admin $admin) => $admin->branches()->attach($branch));

        BranchCache::invalidate();
    }

    /**
     * @throws Halt
     */
    public function deleting(Branch $branch): void
    {
        $branch->loadCount([
            'skuStocks',
            'orders' => function (OrderEloquentBuilder $query) {
                $query->withTrashed();
            },
        ]);

        if ($branch->orders_count > 0) {

            self::abortThenLogAttemptDeleteRelationCount(
                $branch,
                trans('Can not delete branch with associated orders.'),
                'orders',
                $branch->orders_count
            );

        }
        if ($branch->sku_stocks_count > 0) {

            self::abortThenLogAttemptDeleteRelationCount(
                $branch,
                trans('Can not delete branch with associated stocks.'),
                'skuStocks',
                $branch->sku_stocks_count
            );

        }
    }

    public function updated(Branch $branch): void
    {
        BranchCache::invalidate();
    }

    public function deleted(Branch $branch): void
    {
        BranchCache::invalidate();
    }

    public function restored(Branch $branch): void
    {
        BranchCache::invalidate();
    }
}
