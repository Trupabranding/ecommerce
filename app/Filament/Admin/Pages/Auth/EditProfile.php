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

namespace App\Filament\Admin\Pages\Auth;

use Domain\Access\Admin\Models\Admin;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Schemas\Schema;
use Filament\Schemas\Components as SchemaComponents;
use Filament\Support\Enums\Alignment;
use Lloricode\Timezone\Timezone;
use Override;

class EditProfile extends \Filament\Auth\Pages\EditProfile
{
    #[Override]
    protected ?string $heading = 'My Profile';

    #[Override]
    protected ?string $subheading = 'Manage your account settings and preferences';

    #[Override]
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                SchemaComponents\Section::make('Profile Information')
                    ->description('Update your professional profile details')
                    ->schema([
                        FileUpload::make('avatar_path')
                            ->label('Profile Photo')
                            ->avatar()
                            ->directory('avatars')
                            ->maxSize(5120)
                            ->columnSpanFull(),

                        $this->getNameFormComponent()
                            ->disabled(fn () => $this->getAdmin()->isZeroDayAdmin())
                            ->helperText('Your display name in the system'),

                        $this->getEmailFormComponent()
                            ->disabled()
                            ->helperText('Contact your administrator to change email'),
                    ])
                    ->columns(2),

                SchemaComponents\Section::make('Regional Settings')
                    ->description('Customize how the system displays for you')
                    ->schema([
                        Select::make('timezone')
                            ->translateLabel()
                            ->options(Timezone::generateList())
                            ->required()
                            ->rule('timezone')
                            ->searchable()
                            ->default(config()->string('app-default.timezone'))
                            ->helperText('All times will be displayed in your timezone'),

                        Select::make('locale')
                            ->label('Language')
                            ->options([
                                'en' => 'English',
                                'es' => 'Español',
                                'fr' => 'Français',
                                'de' => 'Deutsch',
                                'it' => 'Italiano',
                            ])
                            ->default('en')
                            ->helperText('Choose your preferred language'),
                    ])
                    ->columns(2),

                SchemaComponents\Section::make('Account Security')
                    ->description('Keep your account secure')
                    ->schema([
                        TextInput::make('current_password')
                            ->label('Current Password')
                            ->password()
                            ->revealable()
                            ->hidden(),

                        TextInput::make('password')
                            ->label('New Password')
                            ->password()
                            ->revealable()
                            ->hidden(),
                    ])
                    ->collapsed(),
            ]);
    }

    private function getAdmin(): Admin
    {
        return once(function () {
            /** @var Admin $admin */
            $admin = $this->getUser();

            return $admin;
        });
    }
}
