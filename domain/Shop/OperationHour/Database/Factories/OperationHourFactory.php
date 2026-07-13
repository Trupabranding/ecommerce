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

namespace Domain\Shop\OperationHour\Database\Factories;

use Domain\Shop\OperationHour\Enums\OperationHourDay;
use Domain\Shop\OperationHour\Enums\OperationHourType;
use Domain\Shop\OperationHour\Models\OperationHour;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Domain\Shop\OperationHour\Models\OperationHour>
 */
class OperationHourFactory extends Factory
{
    #[\Override]
    protected $model = OperationHour::class;

    #[\Override]
    public function definition(): array
    {
        return [
            'day' => $this->faker->randomElement(OperationHourDay::cases()),
            'from' => self::fixTimezone(
                '0'.Arr::random(['3', '4', '6', '7', '8']).':00:00'
            ),
            'to' => self::fixTimezone(
                Arr::random(['16', '17', '18', '19', '20', '21', '22']).':00:00'
            ),
            'is_open' => $this->faker->boolean(),
            'type' => $this->faker->randomElement(OperationHourType::cases()),
        ];
    }

    public function wholeDay(): self
    {
        return $this->state([
            'is_all_day' => true,
            'from' => self::fixTimezone('00:00:00'),
            'to' => self::fixTimezone('23:59:00'),
        ]);
    }

    public function open(): self
    {
        return $this->state([
            'is_open' => true,
        ]);
    }

    public function wholeWeek(OperationHourType $type): self
    {
        return $this
            ->state([
                'is_open' => true,
                'is_all_day' => false,
                'type' => $type,
            ])
            ->count(7)
            ->sequence(
                ['day' => OperationHourDay::Sunday],
                ['day' => OperationHourDay::Monday],
                ['day' => OperationHourDay::Tuesday],
                ['day' => OperationHourDay::Wednesday],
                ['day' => OperationHourDay::Thursday],
                ['day' => OperationHourDay::Friday],
                ['day' => OperationHourDay::Saturday],
            );
    }

    private static function fixTimezone(string $datetime): Carbon
    {
        return now()
            ->parse($datetime, config()->string('app-default.timezone'))
            ->timezone(config()->string('app.timezone'));
    }
}
