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

use Domain\Shop\Customer\Models\Address;
use Domain\Shop\Customer\Models\Customer;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\RequestFactories\CustomerProfileUpdateRequestFactory;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\putJson;

it('update profile', function () {

    $data = CustomerProfileUpdateRequestFactory::new()
        ->create();

    $customer = loginAsCustomer();

    putJson('api/customers/profile', $data)
        ->assertValid()
        ->assertOk()
        ->assertJson(function (AssertableJson $json) use ($customer) {
            $json
                ->where('data.type', 'customers')
                ->where('data.id', $customer->uuid)
                ->where('data.attributes.uuid', $customer->uuid)
                ->where('data.attributes.email', $customer->email)
                ->where('data.attributes.first_name', $customer->first_name)
                ->where('data.attributes.last_name', $customer->last_name)
                ->where('data.attributes.mobile', $customer->mobile)
                ->where('data.attributes.gender', $customer->gender->value)
                ->where('data.attributes.status', $customer->status->value)
                ->etc();
        });

    assertDatabaseHas(Customer::class, [
        'first_name' => $data['first_name'],
        'last_name' => $data['last_name'],
        'email' => $data['email'],
        'mobile' => $data['mobile'],
        'gender' => $data['gender'],
        'status' => $customer->status,
    ]);

    //    assertDatabaseHas(Address::class, [
    //        'customer_uuid' => $customer->getKey(),
    //    ]);
});
