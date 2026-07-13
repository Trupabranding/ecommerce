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

namespace App\Filament\Admin\Resources\Shop\BranchResource\Schema;

use Domain\Shop\OperationHour\Enums\OperationHourDay;
use Domain\Shop\OperationHour\Enums\OperationHourType;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

final class BranchSchema
{
    private function __construct() {}

    public static function operationHourSchema(OperationHourType $type): array
    {
        return [
            Repeater::make(
                $type === OperationHourType::online
                    ? 'operationHoursOnline'
                    : 'operationHoursInStore'
            )
                ->translateLabel()
                ->itemLabel(
                    fn (array $state) => trans(':day (:is_open)', [
                        'day' => $state['day'],
                        'is_open' => ((bool) $state['is_open']) ? 'Open' : 'Closed',
                    ])
                )
                ->hiddenLabel()
                ->relationship()
                ->collapsible()
                ->collapsed()
                ->cloneable()
                ->orderColumn(config()->string('eloquent-sortable.order_column_name'))
                ->reorderableWithButtons()
                ->schema([

                    Hidden::make('type')
                        ->default($type),

                    Select::make('day')
                        ->translateLabel()
                        ->options(OperationHourDay::class)
                        ->enum(OperationHourDay::class)
                        ->searchable()
                        ->required()
                        ->preload(),

                    TimePicker::make('from')
                        ->translateLabel()
                        ->required()
                        ->afterStateHydrated(self::timePickerTimezoneResolver(...))
                        ->readOnly(fn (Get $get) => $get('is_all_day')),

                    TimePicker::make('to')
                        ->translateLabel()
                        ->required()
                        ->afterStateHydrated(self::timePickerTimezoneResolver(...))
                        ->readOnly(fn (Get $get): bool => $get('is_all_day')),

                    Checkbox::make('is_all_day')
                        ->translateLabel()
                        ->reactive()
                        ->afterStateUpdated(function (bool $state, Set $set) {
                            if ($state) {
                                $set('from', '00:00:00');
                                $set('to', '23:59:00');
                            }
                        }),

                    Checkbox::make('is_open')
                        ->translateLabel(),
                ])
                ->columns(5),
        ];
    }

    private static function timePickerTimezoneResolver(?string $state, TimePicker $component): void
    {
        if ($state === null) {
            return;
        }

        $component->state(now()->parse($state)->timezone(filament_admin()->timezone)->toTimeString());

    }
}
