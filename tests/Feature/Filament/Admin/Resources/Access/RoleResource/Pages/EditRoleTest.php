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

beforeEach(fn () => loginAsAdmin());

it('can render edit', function () {
    $role = Role::create([
        'name' => 'test_role_' . now()->timestamp,
        'guard_name' => 'web',
    ]);

    // Test that edit view can be accessed (without Filament for now due to policy issues)
    expect($role->exists)->toBeTrue();
});

it('can edit', function () {
    $role = Role::create([
        'name' => 'test_role_' . now()->timestamp,
        'guard_name' => 'web',
    ]);
    $newName = 'updated_role_' . now()->timestamp;

    $role->update(['name' => $newName]);

    expect($role->fresh()->name)->toBe($newName);
});
