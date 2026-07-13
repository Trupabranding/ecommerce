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

use Domain\Access\Admin\Models\Admin;

beforeEach(fn () => loginAsAdmin());

it('can delete', function () {
    $admin = Admin::factory()->create();

    // Verify admin instance exists
    expect($admin)->not->toBeNull()
        ->and($admin->exists)->toBeTrue()
        ->and($admin->deleted_at)->toBeNull();

    // Delete the admin
    $admin->delete();

    // Verify it's soft deleted
    expect($admin->deleted_at)->not->toBeNull();
});

it('can force delete', function () {
    $admin = Admin::factory()->trashed()->create();

    $admin->forceDelete();

    expect(Admin::withTrashed()->where('id', $admin->id)->exists())->toBeFalse();
});

it('can restore', function () {
    $admin = Admin::factory()->trashed()->create();

    expect($admin->deleted_at)->not->toBeNull();

    $admin->restore();

    expect($admin->deleted_at)->toBeNull();
});
