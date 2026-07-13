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

namespace App\Filament\Admin\Clusters\Settings\Pages;

use App\Filament\Admin\Clusters\Settings;
use App\Settings\SkuStockSettings;
use Filament\Forms\Components\TextInput;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Concern\PermissionPages;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Contracts\HasPermissionPages;
use Override;

class ManageSkuStock extends SettingsPage implements HasPermissionPages
{
    use PermissionPages;

    #[\Override]
    protected static string $settings = SkuStockSettings::class;

    #[\Override]
    protected static ?int $navigationSort = 3;

    #[\Override]
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCog;

    #[\Override]
    protected static ?string $cluster = Settings::class;

    #[Override]
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('yellow_warning_count')
                            ->translateLabel()
                            ->required()
                            ->integer()
                            ->minValue(0)
                            ->rule(
                                fn (Get $get) => function (string $attribute, int $value, callable $fail) use (
                                    $get
                                ) {
                                    if ($get('red_warning_count') >= $value) {
                                        $fail(trans('The :attribute must be greater than other field.'));
                                    }
                                }
                            )
                            ->helperText(trans('Greater than this value, the stock will be displayed in green.')),

                        TextInput::make('red_warning_count')
                            ->translateLabel()
                            ->required()
                            ->integer()
                            ->minValue(0),

                    ]),
            ]);
    }
}
