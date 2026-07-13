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

namespace App\Http\Controllers\API\Shop\Customer;

use App\Exceptions\RateLimitExceedException;
use App\Http\Controllers\API\Concern\RateLimit;
use App\Http\Requests\API\Shop\Customer\LoginRequest;
use Domain\Shop\Customer\Enums\CustomerStatus;
use Domain\Shop\Customer\Models\Customer;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Timebox;
use Spatie\RouteAttributes\Attributes\Post;
use Spatie\RouteAttributes\Attributes\Prefix;

/**
 * @tags Customer
 */
#[Prefix('customers')]
class LoginController
{
    use RateLimit;

    /**
     * Login
     *
     * @unauthenticated
     */
    #[Post(uri: 'login', name: 'customers.login')]
    public function __invoke(LoginRequest $request): mixed
    {
        try {
            $this->rateLimit();
        } catch (RateLimitExceedException $e) {
            abort(429, $e->getMessage());
        }

        /** @var Customer $customer */
        $customer = (new Timebox)
            ->call(function (Timebox $timebox) use ($request): Customer {
                $customer = Customer::where('email', $request->validated('email'))
                    ->where('status', CustomerStatus::active)
                    ->first();

                if ($customer?->password === null || ! Hash::check($request->validated('password'), $customer->password)) {
                    throw new AuthenticationException(trans('The provided credentials are incorrect.'));
                }

                return $customer;
            }, 200 * 1_000);

        return response([
            'token' => $customer
                ->createToken(
                    name: 'customer',
                    expiresAt: now()->addDays(2)
                )
                ->plainTextToken,
        ]);
    }
}
