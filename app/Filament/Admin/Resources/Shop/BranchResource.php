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

namespace App\Filament\Admin\Resources\Shop;

use App\Filament\Admin\Resources\Access\ActivityResource\RelationManagers\ActivitiesRelationManager;
use App\Filament\Admin\Resources\Shop\BranchResource\Pages\CreateBranch;
use App\Filament\Admin\Resources\Shop\BranchResource\Pages\EditBranch;
use App\Filament\Admin\Resources\Shop\BranchResource\Pages\ListBranches;
use App\Filament\Admin\Resources\Shop\BranchResource\Schema\BranchSchema;
use Domain\Shop\Branch\Enums\BranchStatus;
use Domain\Shop\Branch\Models\Branch;
use Domain\Shop\OperationHour\Enums\OperationHourType;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Email;
use Override;

class BranchResource extends Resource
{
    #[\Override]
    protected static ?string $model = Branch::class;

    #[\Override]
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingStorefront;

    #[\Override]
    protected static ?int $navigationSort = 8;

    #[\Override]
    protected static ?string $recordTitleAttribute = 'name';

    private static function isBranchFeatureEnabled(): bool
    {
        return config()->boolean('app-default.branch_feature_enabled');
    }

    #[Override]
    public static function shouldRegisterNavigation(): bool
    {
        return self::isBranchFeatureEnabled();
    }

    #[Override]
    public static function canAccess(): bool
    {
        return self::isBranchFeatureEnabled() && parent::canAccess();
    }

    #[Override]
    public static function canViewAny(): bool
    {
        return self::isBranchFeatureEnabled() && parent::canViewAny();
    }

    #[Override]
    public static function getNavigationGroup(): ?string
    {
        return trans('Shop');
    }

    #[Override]
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([

                Tabs::make()
                    ->tabs([

                        Tab::make(trans('Main'))
                            ->schema([

                                Group::make()
                                    ->schema([

                                        Section::make([
                                            TextInput::make('code')
                                                ->translateLabel()
                                                ->required()
                                                ->unique(ignoreRecord: true)
                                                ->minValue(3)
                                                ->live(onBlur: true)
                                                ->afterStateUpdated(
                                                    fn (Set $set, string $state) => $set(
                                                        'code',
                                                        (string) Str::of($state)
                                                            ->upper()
                                                            ->replace(' ', '_')
                                                            ->trim()
                                                    )
                                                )
                                                ->alphaDash(),

                                            TextInput::make('name')
                                                ->translateLabel()
                                                ->required()
                                                ->unique(ignoreRecord: true),
                                        ])
                                            ->columns(2),

                                        Section::make()
                                            ->schema([
                                                Textarea::make('address')
                                                    ->translateLabel()
                                                    ->nullable()
                                                    ->columnSpanFull(),

                                                TextInput::make('email')
                                                    ->translateLabel()
                                                    ->nullable()
                                                    ->rule(Email::default()),

                                                TextInput::make('phone')
                                                    ->translateLabel()
                                                    ->nullable(),

                                                TextInput::make('website')
                                                    ->translateLabel()
                                                    ->nullable()
                                                    ->url()
                                                    ->columnSpanFull(),
                                            ])
                                            ->columns(2),

                                        Section::make(trans('Images'))
                                            ->schema([
                                                SpatieMediaLibraryFileUpload::make('image')
                                                    ->translateLabel()
                                                    ->collection('image')
                                                    ->multiple()
                                                    ->reorderable()
                                                    ->maxFiles(5),

                                                SpatieMediaLibraryFileUpload::make('panel')
                                                    ->translateLabel()
                                                    ->collection('panel'),
                                            ])
                                            ->collapsible(),

                                    ])
                                    ->columnSpan(['lg' => 2]),

                                Group::make()
                                    ->schema([
                                        Section::make(trans('Status'))
                                            ->schema([

                                                ToggleButtons::make('status')
                                                    ->translateLabel()
                                                    ->options(BranchStatus::class)
                                                    ->enum(BranchStatus::class)
                                                    ->required(),

                                                Toggle::make('is_operation_hours_enabled')
                                                    ->label(trans('Operation hours enabled'))
                                                    ->translateLabel()
                                                    ->reactive(),

                                                TextInput::make('maximum_advance_booking_days')
                                                    ->translateLabel()
                                                    ->nullable()
                                                    ->minValue(0)
                                                    ->maxValue(60)
                                                    ->numeric(),
                                            ]),

                                        Section::make(trans('Order Notification'))
                                            ->schema([

                                                Select::make('admin_notify_receiver_id')
                                                    ->translateLabel()
                                                    ->multiple()
                                                    ->nullable()
                                                    ->relationship('adminNotifications', 'name')
                                                    ->searchable()
                                                    ->preload()
                                                    ->optionsLimit(50)
                                                    ->helperText(trans('If not specified, order setting will be used.')),
                                            ]),

                                        Section::make()
                                            ->schema([
                                                TextEntry::make('created_at')
                                                    ->translateLabel()
                                                    ->state(fn (Branch $record): ?string => $record->created_at?->diffForHumans()),

                                                TextEntry::make('updated_at')
                                                    ->translateLabel()
                                                    ->state(fn (Branch $record): ?string => $record->updated_at?->diffForHumans()),
                                            ])
                                            ->hiddenOn('create'),
                                    ])
                                    ->columnSpan(['lg' => 1]),

                            ])->columns(3),

                        Tab::make(trans('Operation Hours Online'))
                            ->visible(fn (Get $get) => $get('is_operation_hours_enabled'))
                            ->schema(BranchSchema::operationHourSchema(OperationHourType::online)),

                        Tab::make(trans('Operation Hours in Store'))
                            ->visible(fn (Get $get) => $get('is_operation_hours_enabled'))
                            ->schema(BranchSchema::operationHourSchema(OperationHourType::in_store)),

                    ])
                    ->persistTabInQueryString()
                    ->columnSpanFull(),

            ]);
    }

    /** @throws Exception */
    #[Override]
    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                SpatieMediaLibraryImageColumn::make('panel_image')
                    ->translateLabel()
                    ->collection('panel')
                    ->conversion('thumb')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->circular(),

                SpatieMediaLibraryImageColumn::make('image')
                    ->translateLabel()
                    ->collection('image')
                    ->conversion('thumb')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->circular(),

                TextColumn::make('code')
                    ->translateLabel()
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('name')
                    ->translateLabel()
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status')
                    ->translateLabel()
                    ->badge()
                    ->sortable(),

                TextColumn::make('operationHoursOnline')
                    ->translateLabel()
                    ->state(function (Branch $record) {
                        if ($record->is_operation_hours_enabled) {
                            return $record->operationHoursHumanReadable(OperationHourType::online);
                        }

                        return trans('Disabled feature.');
                    })
                    ->bulleted(
                        fn (Branch $record, array|string $state) => is_array($state) &&
                            $record->is_operation_hours_enabled
                    )
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->default(new HtmlString('&mdash;')),

                TextColumn::make('operationHoursInStore')
                    ->translateLabel()
                    ->state(function (Branch $record) {
                        if ($record->is_operation_hours_enabled) {
                            return $record->operationHoursHumanReadable(OperationHourType::in_store);
                        }

                        return trans('Disabled feature.');
                    })
                    ->bulleted(
                        fn (Branch $record, array|string $state) => is_array($state) &&
                            $record->is_operation_hours_enabled
                    )
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->default(new HtmlString('&mdash;')),

                TextColumn::make('maximum_advance_booking_days')
                    ->translateLabel()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->translateLabel()
                    ->sortable()
                    ->since()
                    ->dateTimeTooltip(),

                TextColumn::make('created_at')
                    ->translateLabel()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable()
                    ->since()
                    ->dateTimeTooltip(),

                TextColumn::make('deleted_at')
                    ->translateLabel()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable()
                    ->since()
                    ->dateTimeTooltip(),
            ])
            ->filters([

                SelectFilter::make('status')
                    ->translateLabel()
                    ->options(BranchStatus::class),

                //                Tables\Filters\TernaryFilter::make('operation_hours_feature')
                //                    ->translateLabel()
                //                    ->trueLabel(trans('Enabled'))
                //                    ->falseLabel(trans('Disabled'))
                //                    ->queries(
                //                        true: fn (Builder $query) => $query->withTrashed(),
                //                        false: fn (Builder $query) => $query->onlyTrashed(),
                //                    ),

                TrashedFilter::make()
                    ->translateLabel(),
            ])
            ->recordActions([
                Action::make('panel_dashboard')
                    ->translateLabel()
                    ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                    ->url(
                        fn (Branch $record): string => route('filament.branch.pages.main-dashboard', $record),
                        shouldOpenInNewTab: true
                    )
                    ->visible(
                        fn (Branch $record): bool => filament_admin()->canAccessTenant($record)
                    ),

                EditAction::make()
                    ->translateLabel(),

                ActionGroup::make([

                    DeleteAction::make()
                        ->translateLabel(),
                    RestoreAction::make()
                        ->translateLabel(),
                    ForceDeleteAction::make()
                        ->translateLabel(),
                ]),
            ])
            ->defaultSort(config()->string('eloquent-sortable.order_column_name'))
            ->reorderable(config()->string('eloquent-sortable.order_column_name'))
            ->paginatedWhileReordering()
            ->groups([
                'status',
            ]);
    }

    #[Override]
    public static function getPages(): array
    {
        return [
            'index' => ListBranches::route('/'),
            'create' => CreateBranch::route('/create'),
            'edit' => EditBranch::route('/{record}/edit'),
        ];
    }

    #[Override]
    public static function getGloballySearchableAttributes(): array
    {
        return ['name'];
    }

    #[Override]
    public static function getRelations(): array
    {
        return [
            ActivitiesRelationManager::class,
        ];
    }

    #[Override]
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ])
            ->with(['operationHoursOnline', 'operationHoursInStore']);
    }
}
