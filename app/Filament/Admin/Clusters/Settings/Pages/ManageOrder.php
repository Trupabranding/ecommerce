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

namespace App\Filament\Admin\Clusters\Settings\Pages;

use App\Filament\Admin\Clusters\Settings;
use App\Settings\OrderSettings;
use Domain\Access\Admin\Models\Admin;
use Domain\Shop\Order\Enums\OrderPaymentMethod;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Str;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Concern\PermissionPages;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Contracts\HasPermissionPages;
use Override;

class ManageOrder extends SettingsPage implements HasPermissionPages
{
    use PermissionPages;

    #[\Override]
    protected static string $settings = OrderSettings::class;

    #[\Override]
    protected static ?int $navigationSort = 2;

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
                    ->heading(trans('Order Numbering'))
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('prefix')
                            ->translateLabel()
                            ->required()
                            ->minValue(3)
                            ->live(onBlur: true)
                            ->afterStateUpdated(
                                fn (Set $set, ?string $state) => $set(
                                    'prefix',
                                    (string) Str::of($state ?? '')
                                        ->upper()
                                        ->replace(' ', '_')
                                        ->trim()
                                )
                            )
                            ->alphaDash(),
                    ]),

                Section::make()
                    ->heading(trans('Booking Rules'))
                    ->columnSpanFull()
                    ->schema([

                        TextInput::make('maximum_advance_booking_days')
                            ->translateLabel()
                            ->required()
                            ->minValue(0)
                            ->maxValue(60)
                            ->numeric()
                            ->helperText(trans('Default when branch has no specified.')),

                        TextInput::make('daily_order_limit')
                            ->translateLabel()
                            ->required()
                            ->minValue(0)
                            ->maxValue(50000)
                            ->numeric()
                            ->helperText(trans('0 means unlimited.')),

                        TextInput::make('auto_cancel_unpaid_minutes')
                            ->translateLabel()
                            ->required()
                            ->minValue(0)
                            ->maxValue(10080)
                            ->numeric()
                            ->helperText(trans('0 means disabled.')),
                    ])
                    ->columns(3),

                Section::make()
                    ->heading(trans('Checkout'))
                    ->columnSpanFull()
                    ->schema([
                        Toggle::make('allow_guest_checkout')
                            ->translateLabel()
                            ->inline(false)
                            ->required(),

                        ToggleButtons::make('allowed_payment_methods')
                            ->translateLabel()
                            ->options(OrderPaymentMethod::class)
                            ->required()
                            ->multiple()
                            ->inline(),

                        Select::make('default_payment_method')
                            ->translateLabel()
                            ->options(OrderPaymentMethod::class)
                            ->nullable()
                            ->required(fn (Get $get): bool => filled($get('allowed_payment_methods')))
                            ->rule(
                                fn (Get $get) => function (string $attribute, ?string $value, callable $fail) use ($get): void {
                                    if (blank($value)) {
                                        return;
                                    }

                                    $allowed = (array) $get('allowed_payment_methods');

                                    if (! in_array($value, $allowed, true)) {
                                        $fail(trans('The :attribute must be one of the allowed payment methods.'));
                                    }
                                }
                            ),
                    ])
                    ->columns(1),

                Section::make()
                    ->heading(trans('Notifications'))
                    ->columnSpanFull()
                    ->schema([

                        Select::make('admin_notification_ids')
                            ->label('Admin Notifications')
                            ->translateLabel()
                            ->multiple()
                            // TODO: add limit on result options for dropdown
                            ->options(Admin::pluck('name', 'uuid'))
                            ->getSearchResultsUsing(
                                fn (string $search): array => Admin::where('name', 'like', "%{$search}%")
                                    ->limit(50)
                                    ->orderBy('name')
                                    ->pluck('name', 'uuid')
                                    ->toArray()
                            )
                            ->searchable()
                            ->required(),
                            ]),
            ]);
    }
}
