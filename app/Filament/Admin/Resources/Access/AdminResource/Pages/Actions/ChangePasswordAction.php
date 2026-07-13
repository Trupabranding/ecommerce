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

namespace App\Filament\Admin\Resources\Access\AdminResource\Pages\Actions;

use Domain\Access\Admin\Actions\UpdateAdminPasswordAction;
use Domain\Access\Admin\Models\Admin;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Illuminate\Validation\Rules\Password;
use Override;

class ChangePasswordAction extends Action
{
    #[Override]
    public static function getDefaultName(): ?string
    {
        return 'changePassword';
    }

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->translateLabel()
            ->icon(Heroicon::OutlinedLockClosed)
            ->schema([
                TextInput::make('new_password')
                    ->translateLabel()
                    ->password()
                    ->required()
                    ->confirmed()
                    ->rule(Password::default()),
                TextInput::make('new_password_confirmation')
                    ->translateLabel()
                    ->password(),
            ])
            ->action(function (Admin $record, array $data) {
                app(UpdateAdminPasswordAction::class)
                    ->execute($record, $data['new_password'])
                    ? Notification::make()
                        ->title(trans(':value password updated successfully!', ['value' => $record->name]))
                        ->success()
                        ->send()
                    : Notification::make()
                        ->title(trans(':value password updated failed!', ['value' => $record->name]))
                        ->danger()
                        ->send();
            })
            ->authorize(
                fn (Admin $record) => Filament::auth()
                    ->user()
                    ?->can('updatePassword', $record) ?? false
            );

    }
}
