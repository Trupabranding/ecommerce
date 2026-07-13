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

use Domain\Access\Admin\Models\Admin;
use Domain\Access\Role\Models\Role;
use Domain\Shop\Branch\Models\Branch;
use Domain\Shop\OperationHour\Enums\OperationHourType;
use Domain\Shop\OperationHour\Models\OperationHour;
use Illuminate\Container\Attributes\Context;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Spatie\Permission\PermissionRegistrar;

class BranchSeeder extends Seeder
{
    public function run(
        #[Context(ContextSeeder::admin_demo->value)]
        Admin $adminDemo,
    ): void {
        if (! config()->boolean('app-default.branch_feature_enabled')) {
            return;
        }

        if (Branch::query()->exists()) {
            return;
        }

        /** @var \Domain\Shop\Branch\Models\Branch[] $branches */
        $branches = Branch::factory()
            ->enabled()
            ->hasRandomMedia(collectionName: 'panel')
            ->hasRandomMedia()
            ->has(
                OperationHour::factory()
                    ->open()
                    ->wholeWeek(OperationHourType::online)
                    ->wholeDay()
            )
            ->has(
                OperationHour::factory()
                    ->open()
                    ->wholeWeek(OperationHourType::in_store)
                    ->wholeDay()
            )
            ->count(2)
            ->sequence(
                ['name' => 'Branch 1'],
                ['name' => 'Branch 2'],
            )
            ->create();

        $adminDemo->branches()
            ->attach($branches);

        /** @var Role $role */
        $role = app(PermissionRegistrar::class)->getRoleClass();

        foreach ($branches as $branch) {
            Admin::factory()
                ->hasAttached($branch)
                ->createOne([
                    'name' => 'Demo '.$branch->name,
                    'email' => Str::kebab($branch->name).'.ecommerce@mail.test',
                ])
                ->assignRole($role::findByNameOnceCached('branch'));
        }
    }
}
