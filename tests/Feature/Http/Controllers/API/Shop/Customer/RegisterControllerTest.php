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

use Domain\Shop\Customer\Models\Customer;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;
use Tests\RequestFactories\RegisterRequestFactory;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseEmpty;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\postJson;

it('register', function () {

    $data = RegisterRequestFactory::new()->create();

    assertDatabaseEmpty(Customer::class);

    Event::fake(Registered::class);

    postJson('api/customers/register', $data)
        ->assertValid()
        ->assertCreated();

    Event::assertDispatched(Registered::class);

    unset($data['password'], $data['password_confirmation']);

    assertDatabaseCount(Customer::class, 1);
    assertDatabaseHas(Customer::class, $data);
});

it('can not use existing email', function () {

    $customer = Customer::factory()->createOne();

    $data = RegisterRequestFactory::new()
        ->create(['email' => $customer->email]);

    postJson('api/customers/register', $data)
        ->assertInvalid(['email' => 'The email has already been taken.'])
        ->assertUnprocessable();
});
