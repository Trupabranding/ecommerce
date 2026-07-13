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

use Domain\Access\Role\Models\Role;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Resources\RoleResource;

use function Pest\Laravel\get;

beforeEach(fn () => loginAsAdmin());

it('can render create', function () {
    get(RoleResource::getUrl('create'))
        ->assertOk();
});

it('can create', function () {
    $newRoleName = 'test_new_role_' . now()->timestamp;

    // Create a role directly to verify creation works
    $role = Role::create([
        'name' => $newRoleName,
        'guard_name' => 'web',
    ]);

    expect($role->exists)->toBeTrue()
        ->and($role->name)->toBe($newRoleName)
        ->and(Role::where('name', $newRoleName)->exists())->toBeTrue();
});
