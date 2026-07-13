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

namespace Domain\Shop\OperationHour\Actions;

use Domain\Shop\Branch\Models\Branch;
use Domain\Shop\OperationHour\Enums\OperationHourType;
use Domain\Shop\OperationHour\Models\OperationHour;
use Illuminate\Support\Facades\Auth;
use Mockery\Exception;
use Spatie\OpeningHours\OpeningHours;

class GetOpeningHoursByBranchAction
{
    public function execute(Branch $branch, ?OperationHourType $type = null): OpeningHours
    {

        $openingHoursArgument = [];

        $timezoneOutput = Auth::user()->timezone ?? config()->string('app-default.timezone');

        self::operationHours($branch, $type)
            ->each(function (OperationHour $operationHour) use ($timezoneOutput, &$openingHoursArgument) {

                if (! $operationHour->is_open) {
                    return;
                }

                $from = $operationHour->from->timezone($timezoneOutput)->format('H:i');
                $to = $operationHour->to->timezone($timezoneOutput)->format('H:i');

                if (isset($openingHoursArgument[$operationHour->day->value])) {
                    $openingHoursArgument[$operationHour->day->value]['hours'][] = "$from-$to";
                } else {
                    $openingHoursArgument[$operationHour->day->value] = [
                        'hours' => ["$from-$to"],
                        //                    'data' => '',
                    ];
                }
            });

        //        $openingHoursArgument['exceptions'] = [
        //            '2016-11-11' => ['09:00-12:00'],
        //            '2016-12-25' => [],
        //            '01-01'      => [],                // Recurring on each 1st of January
        //            '12-25'      => ['09:00-12:00'],   // Recurring on each 25th of December
        //        ];

        return OpeningHours::create(
            data: $openingHoursArgument,
            //            timezone: config()->string('app.timezone'),
            //            outputTimezone: Auth::user()?->timezone
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|\Domain\Shop\OperationHour\Models\OperationHour[]
     */
    private static function operationHours(Branch $branch, ?OperationHourType $type): \Illuminate\Database\Eloquent\Collection
    {
        $operationHours = match ($type) {
            null => function () use ($branch) {

                if (! $branch->relationLoaded('operationHours')) {
                    throw new Exception($branch::class.'::operationHours not eager loaded.');
                }

                return $branch->operationHours;
            },
            OperationHourType::online => function () use ($branch) {

                if (! $branch->relationLoaded('operationHoursOnline')) {
                    throw new Exception($branch::class.'::operationHoursOnline not eager loaded.');
                }

                return $branch->operationHoursOnline;
            },
            OperationHourType::in_store => function () use ($branch) {

                if (! $branch->relationLoaded('operationHoursInStore')) {
                    throw new Exception($branch::class.'::operationHoursInStore not eager loaded.');
                }

                return $branch->operationHoursInStore;
            },
        };

        return $operationHours();
    }
}
