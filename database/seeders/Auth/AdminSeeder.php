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

use Database\Seeders\ContextSeeder;
use Domain\Access\Admin\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Context;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Config\PermissionConfig;
use Spatie\Permission\Contracts\Role as RoleContract;

class AdminSeeder extends Seeder
{
    public function __construct(protected RoleContract $roleContract) {}

    public function run(): void
    {
        $superAdminHashPassword = config()->string('seeder.admin_hash_password');
        $superAdminName = config()->string('seeder.admin_name');
        $superAdminEmail = config()->string('seeder.admin_email');

        if (blank($superAdminHashPassword)) {
            $this->command->getOutput()->error('SUPER_ADMIN_PASSWORD_HASH not defined in .env file.');

            return;
        }

        $superAdmin = Admin::query()
            ->firstOrCreate(
                ['email' => $superAdminEmail],
                [
                    'name' => $superAdminName,
                    'password' => $superAdminHashPassword,
                    'email_verified_at' => now(),
                ]
            );
        $superAdmin->assignRole(PermissionConfig::superAdmin());
        unset($superAdminHashPassword);

        $admin = Admin::query()
            ->firstOrCreate(
                ['email' => 'admin.ecommerce@mail.test'],
                ['name' => 'Admin', 'email_verified_at' => now()]
            );
        $admin->assignRole(PermissionConfig::admin());

        $employee = Admin::query()
            ->firstOrCreate(
                ['email' => 'employee.ecommerce@mail.test'],
                ['name' => 'Employee', 'email_verified_at' => now()]
            );
        $employee->assignRole('employee');

        $employee2 = Admin::query()
            ->firstOrCreate(
                ['email' => 'employee2.ecommerce@mail.test'],
                ['name' => 'Employee 2', 'email_verified_at' => now()]
            );
        $employee2->assignRole('employee');

        Context::add(
            ContextSeeder::admin_demo->value,
            tap(
                Admin::query()
                    ->firstOrCreate(
                        ['email' => 'demo.ecommerce@mail.test'],
                        [
                            'name' => 'Demo',
                            'password' => app()->isLocal() ? 'secret' : 'K5D@P^y#Z9v778v7DX9u3#T@mNfVmS',
                            'email_verified_at' => now(),
                        ]
                    ),
                fn (Admin $admin): Admin => tap($admin)->assignRole('demo')
            )
        );

        $otpAdmin = Admin::query()->where('email', 'otp.ecommerce@mail.test')->first();

        if ($otpAdmin === null) {
            $otpAdmin = Admin::factory()
                ->withGoogle2fa()
                ->createOne([
                    'name' => 'OTP',
                    'email' => 'otp.ecommerce@mail.test',
                    'password' => app()->environment('local', 'testing') ? 'secret' : '3CMLPJRWJOYEDEQL',
                ]);
        }

        $otpAdmin->assignRole('demo');

        Admin::query()->firstOrCreate(
            ['email' => 'no-roles.ecommerce@mail.test'],
            ['name' => 'No Role', 'email_verified_at' => now()]
        );
    }
}
