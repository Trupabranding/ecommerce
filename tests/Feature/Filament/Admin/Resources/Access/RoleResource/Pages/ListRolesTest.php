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

use Domain\Access\Role\Models\Role;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Resources\RoleResource;

use function Pest\Laravel\get;

beforeEach(fn () => loginAsAdmin());

it('can render index', function () {
    get(RoleResource::getUrl())
        ->assertOk();
});

it('can index list', function () {
    // Create roles to verify listing works
    $role1 = Role::create([
        'name' => 'test_role_' . now()->timestamp,
        'guard_name' => 'web',
    ]);
    $role2 = Role::create([
        'name' => 'test_role2_' . now()->timestamp,
        'guard_name' => 'web',
    ]);

    // Verify roles can be retrieved
    $roles = Role::all();
    expect($roles->count())->toBeGreaterThanOrEqual(2)
        ->and($roles->pluck('name'))->toContain($role1->name, $role2->name);
});
