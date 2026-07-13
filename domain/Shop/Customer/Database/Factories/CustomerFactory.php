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

namespace Domain\Shop\Customer\Database\Factories;

use Database\Factories\Support\HasMediaFactory;
use Domain\Shop\Customer\Enums\CustomerGender;
use Domain\Shop\Customer\Enums\CustomerStatus;
use Domain\Shop\Customer\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Domain\Shop\Customer\Models\Customer>
 */
class CustomerFactory extends Factory
{
    use HasMediaFactory;

    #[\Override]
    protected $model = Customer::class;

    #[\Override]
    public function definition(): array
    {
        return [
            'email' => $this->faker->unique()->safeEmail(),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'mobile' => $this->faker->phoneNumber(),
            'landline' => $this->faker->phoneNumber(),
            'password' => $this->faker->password(),
            'status' => Arr::random(CustomerStatus::cases()),
            'gender' => Arr::random(CustomerGender::cases()),
            'timezone' => config()->string('app-default.timezone'),
        ];
    }

    public function active(): self
    {
        return $this->state(['status' => CustomerStatus::active]);
    }

    #[\Override]
    public function configure(): self
    {
        return $this
            ->afterCreating(function (Customer $customer) {
                self::seedRandomMedia($customer);
            });
    }
}
