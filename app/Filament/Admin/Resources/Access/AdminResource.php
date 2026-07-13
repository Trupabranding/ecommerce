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

namespace App\Filament\Admin\Resources\Access;

use App\Filament\Admin\Resources\Access\ActivityResource\RelationManagers\ActionsRelationManager;
use App\Filament\Admin\Resources\Access\ActivityResource\RelationManagers\ActivitiesRelationManager;
use App\Filament\Admin\Resources\Access\AdminResource\Pages\Actions\impersonateAction;
use Closure;
use Domain\Access\Admin\Actions\UpdateAdminPasswordAction;
use Domain\Access\Admin\Models\Admin;
use Domain\Access\Role\Models\EloquentBuilder\RoleEloquentBuilder;
use Domain\Access\Role\Models\Role;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\Rules\Email;
use Illuminate\Validation\Rules\Password;
use Lloricode\Timezone\Timezone;
use Spatie\Permission\PermissionRegistrar;

class AdminResource extends Resource
{
    #[\Override]
    protected static ?string $model = Admin::class;

    #[\Override]
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    #[\Override]
    protected static ?int $navigationSort = 1;

    #[\Override]
    protected static ?string $recordTitleAttribute = 'name';

    #[\Override]
    public static function getNavigationGroup(): ?string
    {
        return trans('Access');
    }

    #[\Override]
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([

                        Forms\Components\TextInput::make('name')
                            ->translateLabel()
                            ->required(),

                        Forms\Components\TextInput::make('email')
                            ->translateLabel()
                            ->required()
                            ->email()
                            ->rule(fn () => Email::default())
                            ->unique(ignoreRecord: true)
                            ->disabledOn('edit'),

                        \Filament\Schemas\Components\Group::make([
                            Forms\Components\TextInput::make('password')
                                ->translateLabel()
                                ->password()
                                ->revealable()
                                ->nullable()
                                ->rule(Password::default())
                                ->confirmed(),
                            Forms\Components\TextInput::make('password_confirmation')
                                ->translateLabel()
                                ->password()
                                ->revealable()
                                ->dehydrated(false),
                        ])
                            ->visibleOn('create'),

                        Forms\Components\Select::make('roles')
                            ->translateLabel()
                            ->relationship('roles', 'name', function (RoleEloquentBuilder $query) {
                                if (! (filament_admin()->isSuperAdmin())) {
                                    $query
                                        ->whereNotSuperAdmin();
                                }
                            })
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->exists(
                                table: app(PermissionRegistrar::class)->getRoleClass(),
                                column: 'uuid'
                            )
                            ->rule(fn () => function (string $attribute, string|array $value, Closure $fail): void {

                                // work around fixes with current filament v3.0.45
                                if (is_array($value)) {
                                    $value = $value[0] ?? null;

                                    if ($value === null) {
                                        return;
                                    }
                                }

                                $superAdmin = Role::superAdmin();
                                if (
                                    ! (filament_admin()->isSuperAdmin()) &&
                                    $value === $superAdmin->getKey()
                                ) {
                                    $fail(trans('Not allowed to create [:role] when your not [:role].', [
                                        'role' => $superAdmin->name,
                                    ]));
                                }
                            })
                            ->disabled(function (?Admin $record): bool {

                                if ($record === null) {
                                    return false;

                                }

                                if (filament_admin()->isSuperAdmin()) {
                                    return false;
                                }

                                return $record->isSuperAdmin();
                            })
                            // error:
                            // Add fillable property [roles]
                            // to allow mass assignment on [Domain\Access\Admin\Models\Admin].
                            ->dehydrated(false),

                        Forms\Components\Select::make('timezone')
                            ->translateLabel()
                            ->options(Timezone::generateList())
                            ->required()
                            ->rule('timezone')
                            ->searchable()
                            ->default(config()->string('app-default.timezone')),

                        Forms\Components\Select::make('branches')
                            ->translateLabel()
                            ->relationship('branches', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->helperText(fn () => trans('Add access to branch panel.'))
                            ->visible(fn (): bool => config()->boolean('app-default.branch_feature_enabled'))
                            ->dehydrated(fn (): bool => config()->boolean('app-default.branch_feature_enabled'))
                    ])
                    ->columnSpan(['lg' => fn (?Admin $record) => $record === null ? 3 : 2]),

                Section::make()
                    ->schema([

                        TextEntry::make('created_at')
                            ->translateLabel()
                            ->state(fn (Admin $record): ?string => $record->created_at?->diffForHumans()),

                        TextEntry::make('updated_at')
                            ->translateLabel()
                            ->state(fn (Admin $record): ?string => $record->updated_at?->diffForHumans()),
                    ])
                    ->columnSpan(['lg' => 1])
                    ->hiddenOn('create'),
            ])
            ->columns(3);
    }

    /** @throws Exception */
    #[\Override]
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar')
                    ->translateLabel()
                    ->getStateUsing(
                        fn (Admin $record) => $record->getFilamentAvatarUrl()
                    )
                    ->circular()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('name')
                    ->translateLabel()
                    ->searchable(isIndividual: true)
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->translateLabel()
                    ->searchable(isIndividual: true)
                    ->color(fn (Admin $record) => $record->hasVerifiedEmail() ? Color::Green : Color::Red)
                    ->tooltip(
                        fn (Admin $record) => $record->hasVerifiedEmail()
                        ? trans('Verified Email')
                        : trans('Not Verified Email')
                    )
                    ->sortable(),

                Tables\Columns\TextColumn::make('branches.name')
                    ->translateLabel()
                    ->badge()
                    ->toggleable()
                    ->visible(fn (): bool => config()->boolean('app-default.branch_feature_enabled'))
//                    ->listWithLineBreaks()
//                    ->bulleted()
                    ->default(new HtmlString('&mdash;')),

                Tables\Columns\TextColumn::make('roles.name')
                    ->translateLabel()
                    ->badge()
                    ->toggleable()
//                    ->listWithLineBreaks()
//                    ->bulleted()
                    ->default(new HtmlString('&mdash;')),

                Tables\Columns\TextColumn::make('updated_at')
                    ->translateLabel()
                    ->sortable()
                    ->since()
                    ->dateTimeTooltip(),

                Tables\Columns\TextColumn::make('created_at')
                    ->translateLabel()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable()
                    ->since()
                    ->dateTimeTooltip(),

                Tables\Columns\TextColumn::make('deleted_at')
                    ->translateLabel()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable()
                    ->since()
                    ->dateTimeTooltip(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('roles')
                    ->translateLabel()
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload(),

                Tables\Filters\TernaryFilter::make('email_verified_at')
                    ->label(trans('Email Verified'))
                    ->nullable(),

                Tables\Filters\TrashedFilter::make()
                    ->translateLabel(),
            ])
            ->recordActions([
                EditAction::make()
                    ->translateLabel(),

                ActionGroup::make([

                    DeleteAction::make()
                        ->translateLabel(),
                    RestoreAction::make()
                        ->translateLabel(),
                    ForceDeleteAction::make()
                        ->translateLabel(),

                    Action::make('changePassword')
                        ->translateLabel()
                        ->icon(Heroicon::OutlinedLockClosed)
                        ->schema([
                            Forms\Components\TextInput::make('new_password')
                                ->translateLabel()
                                ->required()
                                ->password()
                                ->revealable()
                                ->confirmed()
                                ->rule(Password::default()),
                            Forms\Components\TextInput::make('new_password_confirmation')
                                ->translateLabel()
                                ->password()
                                ->revealable()
                                ->required(),
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
                        ->authorize('updatePassword'),

                    Action::make('resend-verification')
                        ->requiresConfirmation()
                        ->icon(Heroicon::OutlinedEnvelope)
                        ->visible(fn (Admin $record) => ! $record->hasVerifiedEmail())
                        ->action(function (Admin $record, Action $action): void {
                            try {
                                VerifyEmail::$createUrlCallback = fn (MustVerifyEmail $notifiable) => Filament::getVerifyEmailUrl($notifiable);
                                $record->sendEmailVerificationNotification();
                                $action
                                    ->successNotificationTitle(trans('A fresh verification link has been sent to your email address.'))
                                    ->success();
                            } catch (Exception $exception) {
                                report($exception);
                                $action->failureNotificationTitle(trans('Failed to send verification link.'))
                                    ->failure();
                            }
                        })
                        ->authorize('resendEmailVerification'),

                    impersonateAction::make()
                        ->backTo(AdminResource::getUrl()),
                ]),
            ])
            ->defaultSort('updated_at', 'desc')
            ->groups([
                'roles.name',
                Tables\Grouping\Group::make('email_verified_at')
                    ->date()
                    ->collapsible(),
            ]);
    }

    #[\Override]
    public static function getGloballySearchableAttributes(): array
    {
        return [
            'name',
            'email',
        ];
    }

    #[\Override]
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        /** @var \Domain\Access\Admin\Models\Admin $record */
        if (! config()->boolean('app-default.branch_feature_enabled')) {
            return [
                'Roles' => $record->getRoleNames()->implode(','),
            ];
        }

        return [
            'Roles' => $record->getRoleNames()->implode(','),
            'Branches' => $record->branches->implode('name', ','),
        ];
    }

    #[\Override]
    public static function getRelations(): array
    {
        return [
            ActivitiesRelationManager::class,
            ActionsRelationManager::class,
        ];
    }

    #[\Override]
    public static function getPages(): array
    {
        return [
            'index' => AdminResource\Pages\ListAdmins::route('/'),
            'create' => AdminResource\Pages\CreateAdmin::route('/create'),
            'edit' => AdminResource\Pages\EditAdmin::route('/{record}/edit'),
        ];
    }

    #[\Override]
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
