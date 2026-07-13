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

namespace Database\Seeders\Auth;

use Domain\Access\Admin\Models\Admin;
use Domain\Access\Role\Models\Permission;
use Domain\Access\Role\Models\Role;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Database\Seeders\DefaultRoleSeeder;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Enums\PermissionType;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\FilamentPermissionGenerateName;

class RoleSeeder extends DefaultRoleSeeder
{
    /**
     * @throws \Exception
     */
    #[\Override]
    public function run(): void
    {
        parent::run();

        if (! config()->boolean('app-default.branch_feature_enabled')) {
            $branchRole = Role::query()
                ->where('guard_name', 'admin')
                ->where('name', 'branch')
                ->first();

            if ($branchRole !== null) {
                Admin::query()
                    ->role('branch')
                    ->get()
                    ->each(function (Admin $admin) use ($branchRole): void {
                        $admin->removeRole($branchRole);
                    });

                $branchRole->delete();
            }

            Permission::query()
                ->where('guard_name', 'admin')
                ->where(function ($query): void {
                    $query
                        ->where('name', 'branch')
                        ->orWhere('name', 'like', 'branch.%')
                        ->orWhere('name', FilamentPermissionGenerateName::getPanelPermissionName('branch'));
                })
                ->delete();
        }

        /** @var Role $employeeRole */
        $employeeRole = $this->roleContract->findOrCreate(
            name: 'employee',
            guardName: 'admin',
        );
        $employeeRole->givePermissionTo([
            FilamentPermissionGenerateName::getPanelPermissionName('admin'),
            'order',
            'customer',
        ]);

        $demoPermissions = [
            'admin.viewAny',
            'role.viewAny',
            'role.view',
            'order',
            'customer',
            'activity',
            'category',
            'brand',
            'product',
            //'attribute', // TODO: fix There is no permission named `attribute` for guard `admin`.
            'skuStock',
            PermissionType::panels->value,
            PermissionType::widgets->value,
            PermissionType::pages->value,
        ];

        if (config()->boolean('app-default.branch_feature_enabled')) {
            $demoPermissions[] = 'branch';
        }

        /** @var Role $demoRole */
        $demoRole = $this->roleContract->findOrCreate(
            name: 'demo',
            guardName: 'admin',
        );
        $demoRole->givePermissionTo($demoPermissions);

    }
}
