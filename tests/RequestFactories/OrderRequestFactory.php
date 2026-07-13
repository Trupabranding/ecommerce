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

namespace Tests\RequestFactories;

use Domain\Shop\Order\Enums\ClaimType;
use Domain\Shop\Order\Enums\OrderPaymentMethod;
use Illuminate\Support\Arr;
use Worksome\RequestFactories\RequestFactory;

class OrderRequestFactory extends RequestFactory
{
    #[\Override]
    public function definition(): array
    {
        return [
            'payment_method' => Arr::random(OrderPaymentMethod::cases()),
            'notes' => $this->faker->boolean() ? $this->faker->sentence() : null,
            'claim_at' => $this->faker
                ->dateTimeBetween('now', '+1 week')
                ->format('Y-m-d H:i'),
            'claim_type' => ClaimType::delivery,
        ];
    }
}
