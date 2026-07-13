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

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Database\Seeders\Auth\AuthSeeder;
use Domain\Shop\Order\Actions\OrderInvoiceAction;
use Domain\Shop\Stock\Enums\SkuStockType;
use Domain\Shop\Stock\Models\SkuStock;
use Illuminate\Database\Seeder;
use Illuminate\Foundation\Console\OptimizeClearCommand;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\PermissionRegistrar;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $branchFeatureEnabled = config()->boolean('app-default.branch_feature_enabled');

        if (config()->string('queue.default') === 'redis') {
            Artisan::call('app:horizon:clear');
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        Storage::disk(config()->string('media-library.disk_name'))
            ->deleteDirectory(config()->string('media-library.prefix'));
        Storage::disk(config()->string('invoices.disk'))
            ->deleteDirectory(OrderInvoiceAction::FOLDER);

        activity()->disableLogging();

        File::deleteDirectory(storage_path('media-library/temp'));

        Mail::fake();
        Notification::fake();

        $primarySeeders = [
            AuthSeeder::class,
        ];

        if ($branchFeatureEnabled) {
            $primarySeeders[] = BranchSeeder::class;
        }

        $primarySeeders = [
            ...$primarySeeders,
            BrandSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
        ];

        $this->call($primarySeeders);

        //        if ( ! app()->isProduction()) {
        $secondarySeeders = [];

        if ($branchFeatureEnabled) {
            $secondarySeeders[] = OrderSeeder::class;
        }

        $secondarySeeders[] = CustomerSeeder::class;

        $this->call($secondarySeeders);
        //        }

        // reset product to base on stock
        SkuStock::query()->update([
            'type' => SkuStockType::base_on_stock,
            'count' => 10,
            'warning' => 7,
        ]);

        Artisan::call(OptimizeClearCommand::class);
    }
}
