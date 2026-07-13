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

namespace Database\Seeders;

use Domain\Shop\Branch\Enums\BranchStatus;
use Domain\Shop\Branch\Models\Branch;
use Domain\Shop\Customer\Models\Address;
use Domain\Shop\Customer\Models\Customer;
use Domain\Shop\Order\Actions\OrderCreatedPipelineAction;
use Domain\Shop\Order\Models\Order;
use Domain\Shop\Product\Enums\ProductStatus;
use Domain\Shop\Product\Models\Sku;
use Domain\Shop\Stock\Models\EloquentBuilder\SkuStockEloquentBuilder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

use function Spatie\PestPluginTestTime\testTime;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        if (! config()->boolean('app-default.branch_feature_enabled')) {
            return;
        }

        if (! Branch::where('status', BranchStatus::enabled)->exists()) {
            return;
        }

        $orderPipeline = app(OrderCreatedPipelineAction::class);

        $this->command
            ->withProgressBar(
                range(1, 10),
                function () use ($orderPipeline) {

                    testTime()->subDay();

                    Customer::factory(['password' => 'secret'])
                        ->count(Arr::random(range(2, 15)))
                        ->has(Address::factory()->count(Arr::random(range(1, 3))))
                        ->active()
                        ->create();

                    self::order($orderPipeline);
                }
            );

        $this->command->newLine();
    }

    private static function order(OrderCreatedPipelineAction $orderPipeline): void
    {
        /** @var \Domain\Shop\Customer\Models\Customer $customer */
        $customer = Customer::where('created_at', '<=', now())
            ->inRandomOrder()
            ->first();

        /** @var \Domain\Shop\Branch\Models\Branch $branch */
        $branch = Branch::where(
            'status',
            BranchStatus::enabled
        )
            ->inRandomOrder()->first();

        if ($branch === null) {
            return;
        }

        $order = Order::factory()
            ->for($branch)
            ->for($customer)
            ->hasOrderItems(
                Sku::whereRelation('product', 'status', ProductStatus::in_stock)
                    ->whereRelation('skuStocks', function (SkuStockEloquentBuilder $query) use ($branch) {
                        $query->whereBelongsTo($branch);
                    })
                    ->inRandomOrder()
                    ->take(4)
                    ->get()
            )
            ->createOne();

        $orderPipeline->execute($order);
    }
}
