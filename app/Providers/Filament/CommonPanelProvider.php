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

namespace App\Providers\Filament;

use App\Providers\Macros\FilamentActionMixin;
use App\Providers\Macros\FilamentMountableActionMixin;
use App\Providers\Macros\FilamentTextInputMixin;
use BezhanSalleh\LanguageSwitch\LanguageSwitch;
use Filament\Actions\Action;
use Filament\Actions\Exports\Models\Export;
use Filament\Actions\Imports\Models\Import;
use Filament\Actions\MountableAction;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Infolists;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Facades\FilamentTimezone;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\ValidationException;

class CommonPanelProvider extends ServiceProvider
{
    public function boot(): void
    {
        self::configureComponents();

        self::registerMacros();

        Page::$reportValidationErrorUsing = function (ValidationException $exception) {
            Notification::make()
                ->title($exception->getMessage())
                ->danger()
                ->send();
        };

        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch
                ->locales(['ar', 'en', 'fr'])
                ->circular();
        });

        // https://github.com/filamentphp/filament/issues/10002#issuecomment-1837511287
        Import::polymorphicUserRelationship();

        // https://filamentphp.com/docs/3.x/actions/prebuilt-actions/export#using-a-polymorphic-user-relationship
        Export::polymorphicUserRelationship();

    }

    private static function registerMacros(): void
    {
        //        MountableAction::mixin(new FilamentMountableActionMixin);
        Forms\Components\TextInput::mixin(new FilamentTextInputMixin);
        Action::mixin(new FilamentActionMixin);
    }

    private static function configureComponents(): void
    {
        //        FilamentTimezone::set(filament_admin()->timezone);
        Table::configureUsing(
            fn (Tables\Table $component) => $component
                ->defaultCurrency(config()->string('app-default.currency'))
                ->defaultDateTimeDisplayFormat(config()->string('app-default.date_time_display_format'))
        );
        Schema::configureUsing(
            fn (Schema $component) => $component
                ->defaultCurrency(config()->string('app-default.currency'))
                ->defaultDateTimeDisplayFormat(config()->string('app-default.date_time_display_format'))
        );
        Forms\Components\FileUpload::configureUsing(
            fn (Forms\Components\FileUpload $component) => $component
                ->maxSize(config()->integer('media-library.max_file_size') / 1024)
                ->preserveFilenames()
                ->openable()
                ->downloadable()
                ->panelLayout('grid')
        );

        Infolists\Components\TextEntry::configureUsing(
            function (Infolists\Components\TextEntry $component) {
                $component->lineClamp(1);
                if (Filament::auth()->check()) {
                    $component
                        ->timezone(
                            filament_admin()->timezone
                        );
                }
            }
        );

        Forms\Components\DateTimePicker::configureUsing(
            function (Forms\Components\DateTimePicker $component): void {
                if (Filament::auth()->check()) {
                    $component
                        ->timezone(
                            filament_admin()->timezone
                        )
                        ->native(false)
                        ->weekStartsOnSunday();
                }

                //                $component
                //                    ->seconds(false);
            }
        );

        //        Forms\Components\TimePicker::configureUsing(
        //            function (Forms\Components\TimePicker $component): void {
        //                if (Filament::auth()->check()) {
        //                    $component
        //                        ->afterStateHydrated(function (string $state, Forms\Components\TimePicker $component) {
        //                            $component->state(now()->parse($state)->timezone(filament_admin()->timezone)->toTimeString());
        //                        });
        // //                        ->timezone(
        // //                            filament_admin()->timezone
        // //                        );
        //                }
        //            }
        //        );
        Tables\Columns\TextColumn::configureUsing(
            function (Tables\Columns\TextColumn $column): void {
                $column->lineClamp(1);
                if (Filament::auth()->check()) {
                    $column
                        ->timezone(filament_admin()->timezone);
                }
            }
        );
        Tables\Table::configureUsing(
            fn (Tables\Table $table) => $table
                ->extremePaginationLinks()
                ->defaultPaginationPageOption(10)
        );
    }
}
