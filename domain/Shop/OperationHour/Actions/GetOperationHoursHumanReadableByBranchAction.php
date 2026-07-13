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

namespace Domain\Shop\OperationHour\Actions;

use Domain\Shop\Branch\Models\Branch;
use Domain\Shop\OperationHour\Enums\OperationHourType;
use Spatie\OpeningHours\Day;

readonly class GetOperationHoursHumanReadableByBranchAction
{
    public function __construct(private GetOpeningHoursByBranchAction $getOpeningHoursByBranch) {}

    /**
     * @return array<int, string>
     */
    public function execute(Branch $branch, ?OperationHourType $type = null): array
    {
        $openingHoursAction = collect(
            $this->getOpeningHoursByBranch
                ->execute($branch, $type)
                ->asStructuredData('h:i A')
        )
            ->filter(fn (array $open) => isset($open['dayOfWeek']));

        $output = [];

        foreach ($openingHoursAction->groupBy('closes') as $closeTime => $openingOurs) {

            $dayOfTheWeeksKeyedByOpen = [];
            $openTimes = [];

            foreach ($openingOurs as $openingOur) {
                $dayOfTheWeeksKeyedByOpen[$openingOur['opens']][] = $openingOur['dayOfWeek'];
                $openTimes[] = $openingOur['opens'];
            }

            foreach (collect($openTimes)->unique() as $openTime) {

                $output[] = trans(
                    ':day_range: :from - :to',
                    [
                        'day_range' => self::dayRange($dayOfTheWeeksKeyedByOpen[$openTime]),
                        'from' => $openTime,
                        'to' => $closeTime === '11:59 PM'
                           ? 'Midnight'
                           : $closeTime,
                    ]
                );
            }
        }

        return $output;
    }

    private static function dayRange(array $days): string
    {
        $daysList = collect(Day::cases())->map(fn (Day $day) => $day->value)->toArray();

        /** @var list<int> $dayNumerics */
        $dayNumerics = array_map(fn (string $day) => array_search(strtolower($day), $daysList, true), $days);
        sort($dayNumerics);

        $prevDayNumeric = null;
        $words = [];
        foreach ($dayNumerics as $key => $dayNumeric) {
            if (! is_null($prevDayNumeric) && $prevDayNumeric + 1 === $dayNumeric) {
                $words[] = explode(' to ', (string) $words[$key - 1])[0].' to '.$dayNumeric;
                unset($words[$key - 1]);
            } else {
                $words[] = $dayNumeric;
            }

            $prevDayNumeric = $dayNumeric;
        }

        $final = '';
        foreach (str_split(implode(', ', $words)) as $char) {
            if (is_numeric($char)) {
                $final .= ucfirst((string) $daysList[$char]);
            } else {
                $final .= $char;
            }
        }

        return $final;
    }
}
