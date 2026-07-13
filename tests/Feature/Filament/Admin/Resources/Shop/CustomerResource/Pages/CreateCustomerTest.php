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
use function Pest\Livewire\livewire;

beforeEach(fn () => loginAsAdmin());

it('can render create', function () {
    get(CustomerResource::getUrl('create'))
        ->assertOk();
});

it('can create', function () {
    $newCustomer = Customer::factory()
        ->make();

    livewire(CustomerResource\Pages\CreateCustomer::class)
        ->fillForm([
            'first_name' => $newCustomer->first_name,
            'last_name' => $newCustomer->last_name,
            'email' => $newCustomer->email,
            'mobile' => $newCustomer->mobile,
            'gender' => $newCustomer->gender,
            'status' => $newCustomer->status,
            'timezone' => $newCustomer->timezone,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Customer::where('email', $newCustomer->email)->exists())->toBeTrue();
});
