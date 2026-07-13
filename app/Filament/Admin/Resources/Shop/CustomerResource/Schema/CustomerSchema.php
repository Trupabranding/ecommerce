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

namespace App\Filament\Admin\Resources\Shop\CustomerResource\Schema;

use Domain\Shop\Customer\Enums\CustomerGender;
use Domain\Shop\Customer\Enums\CustomerStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Illuminate\Validation\Rules\Email;
use Illuminate\Validation\Rules\Password;
use Lloricode\Timezone\Timezone;

final class CustomerSchema
{
    private function __construct() {}

    public static function schema(): array
    {
        return [
            SpatieMediaLibraryFileUpload::make('image')
                ->translateLabel()
                ->hiddenLabel()
                ->collection('image')
                ->columnSpanFull(),

            TextInput::make('first_name')
                ->translateLabel()
                ->required(),

            TextInput::make('last_name')
                ->translateLabel()
                ->nullable(),

            TextInput::make('email')
                ->translateLabel()
                ->nullable()
                ->unique(ignoreRecord: true)
                ->email()
                ->rule(fn () => Email::default()),

            TextInput::make('mobile')
                ->translateLabel()
                ->nullable()
                ->string(),

            TextInput::make('landline')
                ->translateLabel()
                ->nullable()
                ->string(),

            Select::make('timezone')
                ->translateLabel()
                ->options(Timezone::generateList())
                ->required()
                ->rule('timezone')
                ->searchable()
                ->default(config()->string('app-default.timezone')),

            TextInput::make('password')
                ->translateLabel()
                ->password()
                ->revealable()
                ->nullable()
                ->rules([Password::defaults(), 'confirmed']),

            TextInput::make('password_confirmation')
                ->translateLabel()
                ->password()
                ->revealable()
                ->nullable()
                ->dehydrated(false),

            ToggleButtons::make('status')
                ->translateLabel()
                ->options(CustomerStatus::class)
                ->enum(CustomerStatus::class)
                ->default(CustomerStatus::active)
                ->inline()
                ->required(),

            ToggleButtons::make('gender')
                ->translateLabel()
                ->inline()
                ->options(CustomerGender::class)
                ->enum(CustomerGender::class),
        ];
    }
}
