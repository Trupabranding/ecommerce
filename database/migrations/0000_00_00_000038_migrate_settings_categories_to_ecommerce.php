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

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Migrate from old SaaS categories (Plan, Process, People, Operations)
     * to new eCommerce categories (Store, Products, Inventory, Payments, Shipping, Notifications, SEO, Integrations)
     */
    public function up(): void
    {
        // Delete old SaaS categories and their associated features
        $oldCategorySlugs = ['plan', 'process', 'people', 'operations'];

        foreach ($oldCategorySlugs as $slug) {
            $category = DB::table('setting_categories')
                ->where('slug', $slug)
                ->first();

            if ($category) {
                // Delete features first (will cascade through relationships)
                DB::table('setting_features')
                    ->where('setting_category_uuid', $category->uuid)
                    ->delete();

                // Delete admin settings
                DB::table('admin_settings')
                    ->where('setting_category_uuid', $category->uuid)
                    ->delete();

                // Delete the category
                DB::table('setting_categories')
                    ->where('uuid', $category->uuid)
                    ->delete();
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration removes old data, so we can't reliably restore it
        // Consider this a one-way migration
    }
};
