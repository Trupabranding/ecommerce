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
use Illuminate\Support\Facades\Route;
use Illuminate\Testing\Fluent\AssertableJson;

use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function PHPUnit\Framework\assertCount;

it('generate token', function () {
    $password = fake()->password();
    $customer = Customer::factory()
        ->active()
        ->createOne([
            'password' => $password,
        ]);

    assertCount(0, $customer->tokens);

    postJson('api/customers/login', [
        'email' => $customer->email,
        'password' => $password,
    ])
        ->assertValid()
        ->assertOk()
        ->assertJson(function (AssertableJson $json) {
            $json->has('token')
                ->whereType('token', 'string');
        });

    assertCount(1, $customer->refresh()->tokens);
});

it('can not generate token w/ invalid credentials', function () {
    $password = fake()->password();
    $customer = Customer::factory()
        ->active()
        ->createOne([
            'password' => $password,
        ]);

    assertCount(0, $customer->tokens);

    postJson('api/customers/login', [
        'email' => $customer->email,
        'password' => $password.'-now-is-wrong',
    ])
        ->assertValid()
        ->assertUnauthorized();

    assertCount(0, $customer->refresh()->tokens);
});

it('can access protected route with valid token', function () {
    $customer = Customer::factory()
        ->active()
        ->createOne();

    $token = $customer
        ->createToken(
            name: 'testing-auth',
        )
        ->plainTextToken;

    Route::get('api/test-private-route', fn () => 'access granted!')
        ->middleware([
            'api',
            'auth:sanctum',
        ]);

    getJson('api/test-private-route', [
        'Authorization' => 'Bearer '.$token,
    ])
        ->assertOk()
        ->assertSee('access granted!');
});

it('can access protected route with invalid token', function (?string $token) {
    Route::get('api/test-private-route', fn () => '')
        ->middleware([
            'api',
            'auth:sanctum',
        ]);

    getJson('api/test-private-route', [
        'Authorization' => $token,
    ])
        ->assertUnauthorized();
})
    ->with([
        null,
        'Bearer invalid',
        '',
    ]);

it('can not access protected route w/out header authorization', function () {
    Route::get('api/test-private-route', fn () => '')
        ->middleware([
            'api',
            'auth:sanctum',
        ]);

    getJson('api/test-private-route')
        ->assertUnauthorized();
});
