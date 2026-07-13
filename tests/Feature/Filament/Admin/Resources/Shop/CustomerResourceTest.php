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

use App\Filament\Admin\Resources\Shop\CustomerResource;
use Domain\Shop\Customer\Models\Customer;

use function Pest\Laravel\get;

beforeEach(fn () => loginAsAdmin());

it('can delete', function () {
    $customer = Customer::factory()->create();

    get(CustomerResource::getUrl('edit', ['record' => $customer]))
        ->assertOk();

    $customer->delete();
    expect($customer->fresh()->trashed())->toBeTrue();
});

it('can force delete', function () {
    $customer = Customer::factory()->create();
    $customer->delete();

    expect(Customer::withTrashed()->where('uuid', $customer->uuid)->exists())->toBeTrue();

    $customer->forceDelete();

    expect(Customer::withTrashed()->where('uuid', $customer->uuid)->exists())->toBeFalse();
});

it('can restore', function () {
    $customer = Customer::factory()->create();
    $customer->delete();

    expect(Customer::withTrashed()->where('uuid', $customer->uuid)->first()->deleted_at)->not->toBeNull();

    $customer->restore();

    expect(Customer::withTrashed()->where('uuid', $customer->uuid)->first()->deleted_at)->toBeNull();
});
