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

use App\Filament\Admin\Resources\Access\ActivityResource\Pages\ListActivities;
use App\Filament\Admin\Resources\Shop\CustomerResource;
use Closure;
use Domain\Access\Admin\Models\Admin;
use Domain\Shop\Customer\Models\Customer;
use ErrorException;
use Exception;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Infolists;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Routing\Exceptions\UrlGenerationException;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Override;
use Spatie\Activitylog\ActivitylogServiceProvider;
use Spatie\Activitylog\Exceptions\InvalidConfiguration;
use Spatie\Activitylog\Models\Activity;

class ActivityResource extends Resource
{
    #[\Override]
    protected static ?int $navigationSort = 3;

    #[\Override]
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedTableCells;

    /**
     * @return class-string<Activity>
     *
     * @throws InvalidConfiguration
     */
    #[Override]
    public static function getModel(): string
    {
        /** @phpstan-ignore return.type */
        return ActivitylogServiceProvider::determineActivityModel();
    }

    #[Override]
    public static function getNavigationGroup(): ?string
    {
        return trans('Access');
    }

    #[Override]
    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([

                TextEntry::make('description')
                    ->translateLabel()
                    ->columnSpanFull(),

                TextEntry::make('subject')
                    ->translateLabel()
                    ->state(
                        function (Activity $record): ?string {
                            if ($record->subject === null) {
                                return null;
                            }

                            /** @var class-string<\Filament\Resources\Resource>|null $resource */
                            $resource = collect(Filament::getResources())
                                ->first(fn (mixed $resource) => $resource::getModel() === $record->subject::class);

                            return $resource !== null
                                ? Str::headline($resource::getModelLabel())
                                : (string) Str::of($record->subject::class)->classBasename()->headline();
                        }
                    )
                    ->url(
                        function (Activity $record): ?string {
                            if ($record->subject === null) {
                                return null;
                            }

                            /** @var class-string<\Filament\Resources\Resource>|null $resource */
                            $resource = collect(Filament::getResources())
                                ->first(fn (mixed $resource) => $resource::getModel() === $record->subject::class);

                            if ($resource === null) {
                                return null;
                            }

                            try {
                                if ($resource::hasPage('view') && $resource::canView($record)) {
                                    return $resource::getUrl('view', ['record' => $record->subject]);
                                }
                                if ($resource::hasPage('edit') && $resource::canEdit($record)) {
                                    return $resource::getUrl('edit', ['record' => $record->subject]);
                                }
                            } catch (UrlGenerationException) {
                            }

                            return null;
                        },
                        shouldOpenInNewTab: true
                    )
                    ->placeholder('--'),

                TextEntry::make('causer')
                    ->translateLabel()
                    ->state(function (Activity $record): ?string {
                        if ($record->causer === null) {
                            return null;
                        }

                        return match ($record->causer::class) {
                            Admin::class => trans('Admin: :admin', ['admin' => $record->causer->name]),
                            Customer::class => trans('Customer: :customer', [
                                'customer' => $record->causer->full_name,
                            ]),
                            default => throw new ErrorException(
                                'No matching model `'.$record->causer::class.'` for activity causer.'
                            ),
                        };
                    })
                    ->url(
                        function (Activity $record): ?string {
                            if ($record->causer === null) {
                                return null;
                            }

                            return match ($record->causer::class) {
                                Admin::class => AdminResource::canEdit($record->causer)
                                    ? AdminResource::getUrl('edit', [$record->causer])
                                    : null,
                                Customer::class => CustomerResource::canEdit($record->causer)
                                    ? CustomerResource::getUrl('edit', [$record->causer])
                                    : null,
                                default => throw new ErrorException(
                                    'No matching model `'.$record->causer::class.'` for activity causer.'
                                ),
                            };
                        },
                        shouldOpenInNewTab: true
                    )
                    ->placeholder('--'),

                TextEntry::make('created_at')
                    ->label('Logged at')
                    ->translateLabel()
                    ->dateTime(),

                Section::make()
                    ->description(trans('Properties'))
                    ->visible(
                        fn (Activity $record): bool => $record
                            ->properties
                            ?->except('old', 'attributes')
                            ->isNotEmpty() ?? false
                    )
                    ->schema([

                        KeyValueEntry::make('properties')
                            ->hiddenLabel()
                            ->inlineLabel(false)
                            ->state(
                                fn (Activity $record): ?Collection => $record
                                    ->properties
                                    ?->except('old', 'attributes')
                            ),

                        //                        Infolists\Components\RepeatableEntry::make('data')
                        //                            ->hiddenLabel()
                        //                            ->state(
                        //                                fn (Activity $record): ?Collection => $record
                        //                                    ->properties
                        //                                    ?->except('old', 'attributes')
                        //                            )
                        //                            ->schema(
                        //                                fn (?Collection $state): array => $state
                        //                                    ?->map(
                        //                                        fn (string $value, string $property): Infolists\Components\TextEntry => Infolists\Components\TextEntry::make($property)
                        //                                            ->color(Color::'primary')
                        //                                            ->state($value)
                        //                                            ->inlineLabel()
                        //                                    )
                        //                                    ->toArray() ?? []
                        //                            )
                        //                            ->contained(false),

                    ]),

                Section::make()
                    ->description(trans('Changes'))
                    ->visible(
                        fn (Activity $record): bool => $record
                            ->properties
                            ?->hasAny('old', 'attributes') ?? false
                    )
                    ->schema([

                        KeyValueEntry::make('old')
                            ->translateLabel()
                            ->inlineLabel(false)
                            ->state(self::changes('old')),

                        KeyValueEntry::make('new')
                            ->translateLabel()
                            ->inlineLabel(false)
                            ->state(self::changes('attributes')),
                    ]),

                Fieldset::make('others')
                    ->hiddenLabel()
                    ->schema([
                        TextEntry::make('event')
                            ->translateLabel()
                            ->badge()
                            ->placeholder('--'),

                        TextEntry::make('log_name')
                            ->translateLabel()
                            ->badge(),

                        TextEntry::make('batch_uuid')
                            ->translateLabel()
                            ->placeholder('--'),
                    ]),

            ])
            ->columns(1)
            ->inlineLabel();
    }

    private static function changes(string $type): Closure
    {
        return function (Activity $record) use ($type) {
            $newProperties = $record->properties
                ?->only($type)
                ->first();

            if ($newProperties === null) {
                return ['' => ''];
            }

            $return = [];

            foreach ($newProperties as $property => $value) {

                if (is_array($value)) {
                    $value = json_encode($value);
                }
                $return[$property] = $value;
            }

            return $return;
        };
    }

    /** @throws Exception */
    #[Override]
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('log_name')
                    ->translateLabel()
                    ->wrap()
                    ->sortable(),

                TextColumn::make('subject_type')
                    ->translateLabel()
                    ->wrap()
                    ->sortable(),

                TextColumn::make('event')
                    ->translateLabel()
                    ->wrap()
                    ->sortable(),

                TextColumn::make('description')
                    ->translateLabel()
                    ->wrap()
                    ->sortable(),

                TextColumn::make('batch_uuid')
                    ->translateLabel()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->translateLabel()
                    ->wrap()
                    ->since()
                    ->dateTimeTooltip(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->translateLabel(),
            ])
            ->filters([
                SelectFilter::make('log_name')
                    ->translateLabel()
                    ->multiple()
                    ->options(fn () => self::getModel()::orderBy('log_name')
                        ->distinct()
                        ->pluck('log_name')
                        ->mapWithKeys(fn (string $value) => [$value => Str::headline($value)])),
                SelectFilter::make('event')
                    ->translateLabel()
                    ->multiple()
                    ->options(fn () => self::getModel()::orderBy('event')
                        ->distinct()
                        ->pluck('event')
                        ->mapWithKeys(fn (?string $value) => [$value ?? 'null' => Str::headline($value ?? 'none')])),
                Filter::make('created_at')
                    ->translateLabel()
                    ->schema([
                        DatePicker::make('logged_from')
                            ->translateLabel(),
                        DatePicker::make('logged_until')
                            ->translateLabel(),
                    ])
                    ->query(
                        fn (Builder $query, array $data): Builder => $query
                            ->when(
                                $data['logged_from'],
                                fn (Builder $query, string $date): Builder => $query
                                    ->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['logged_until'],
                                fn (Builder $query, string $date): Builder => $query
                                    ->whereDate('created_at', '<=', $date),
                            )
                    )
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['logged_from'] ?? null) {
                            $indicators['logged_from'] = trans('Logged from: :date', [
                                'date' => now()->parse($data['logged_from'])->toFormattedDateString(),
                            ]);
                        }

                        if ($data['logged_until'] ?? null) {
                            $indicators['logged_until'] = trans('Logged until: :date', [
                                'date' => now()->parse($data['logged_until'])->toFormattedDateString(),
                            ]);
                        }

                        return $indicators;
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->groups(['log_name', 'event']);
    }

    #[Override]
    public static function getPages(): array
    {
        return [
            'index' => ListActivities::route('/'),
        ];
    }
}
