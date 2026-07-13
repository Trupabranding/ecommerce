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
use App\Settings\SiteSettings;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Lloricode\Timezone\Timezone;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Concern\PermissionPages;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Contracts\HasPermissionPages;
use Override;

class ManageSite extends SettingsPage implements HasPermissionPages
{
    use PermissionPages;

    #[\Override]
    protected static string $settings = SiteSettings::class;

    #[\Override]
    protected static ?int $navigationSort = 1;

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
                    ->heading(trans('Branding'))
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('name')
                            ->translateLabel()
                            ->required(),

                        TextInput::make('legal_name')
                            ->translateLabel()
                            ->nullable(),

                        FileUpload::make('favicon')
                            ->image()
                            ->required()
                            ->openable()
                            ->getUploadedFileNameForStorageUsing(
                                fn (TemporaryUploadedFile $file) => 'favicon.'.$file->extension()
                            ),

                        FileUpload::make('logo')
                            ->image()
                            ->required()
                            ->openable()
                            ->getUploadedFileNameForStorageUsing(
                                fn (TemporaryUploadedFile $file) => 'logo.'.$file->extension()
                            ),
                    ])
                    ->columns(2),

                Section::make()
                    ->heading(trans('Business Profile'))
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('support_email')
                            ->translateLabel()
                            ->email()
                            ->nullable(),

                        TextInput::make('support_phone')
                            ->translateLabel()
                            ->nullable(),

                        TextInput::make('website_url')
                            ->translateLabel()
                            ->url()
                            ->nullable(),

                        TextInput::make('tax_number')
                            ->translateLabel()
                            ->nullable(),

                        TextInput::make('registration_number')
                            ->translateLabel()
                            ->nullable(),

                        Textarea::make('address')
                            ->translateLabel()
                            ->nullable()
                            ->string()
                            ->columnSpanFull(),

                        Textarea::make('invoice_footer')
                            ->translateLabel()
                            ->helperText(trans('Shown in printable order and invoice documents.'))
                            ->nullable()
                            ->string()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make()
                    ->heading(trans('Regional Defaults'))
                    ->columnSpanFull()
                    ->schema([
                        Select::make('timezone')
                            ->translateLabel()
                            ->options(Timezone::generateList())
                            ->required()
                            ->rule('timezone')
                            ->searchable(),

                        TextInput::make('locale')
                            ->translateLabel()
                            ->required()
                            ->regex('/^[a-z]{2}(?:_[A-Z]{2})?$/')
                            ->helperText(trans('Use format like en or en_US.')),

                        TextInput::make('currency')
                            ->translateLabel()
                            ->required()
                            ->minLength(3)
                            ->maxLength(3)
                            ->live(onBlur: true)
                            ->afterStateUpdated(
                                fn (Set $set, ?string $state) => $set('currency', strtoupper((string) $state))
                            )
                            ->alpha(),
                    ])
                    ->columns(3),
            ]);
    }
}
