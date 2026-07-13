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

use Domain\Settings\Models\AdminSetting;
use Domain\Settings\Models\SettingCategory;
use Domain\Settings\Models\SettingFeature;
use Domain\Settings\Models\SettingValue;
use Domain\Shop\Branch\Models\Branch;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('setting_categories', function (Blueprint $table) {
            $table->uuid()->primary()->unique();

            $table->string('slug')->unique();
            $table->string('name');
            $table->text('description')->nullable();

            $table->timestamps();
        });

        Schema::create('admin_settings', function (Blueprint $table) {
            $table->uuid()->primary()->unique();

            $table->string('key')->unique();
            $table->json('value')->nullable();
            $table->text('description')->nullable();

            $table->foreignIdFor(SettingCategory::class)->nullable()->constrained(column: 'uuid');

            $table->boolean('is_global')->default(true);
            $table->boolean('is_feature_toggle')->default(false);

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('setting_features', function (Blueprint $table) {
            $table->uuid()->primary()->unique();

            $table->string('key')->unique();
            $table->string('name');
            $table->text('description')->nullable();

            $table->foreignIdFor(SettingCategory::class)->nullable()->constrained(column: 'uuid');

            $table->boolean('enabled')->default(false);
            $table->json('default_value')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('setting_values', function (Blueprint $table) {
            $table->uuid()->primary()->unique();

            $table->foreignIdFor(SettingFeature::class)->constrained(column: 'uuid')->cascadeOnDelete();
            $table->foreignIdFor(Branch::class)->nullable()->constrained(column: 'uuid')->cascadeOnDelete();

            $table->json('value')->nullable();

            $table->timestamps();

            $table->unique([
                (new SettingFeature)->getForeignKey(),
                (new Branch)->getForeignKey(),
            ], name: 'setting_values_unique_feature_branch');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('setting_values');
        Schema::dropIfExists('setting_features');
        Schema::dropIfExists('admin_settings');
        Schema::dropIfExists('setting_categories');
    }
};
