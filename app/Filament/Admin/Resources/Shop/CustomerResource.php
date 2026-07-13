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
use App\Filament\Admin\Resources\Shop\CustomerResource\Pages\CreateCustomer;
use App\Filament\Admin\Resources\Shop\CustomerResource\Pages\EditCustomer;
use App\Filament\Admin\Resources\Shop\CustomerResource\Pages\ListCustomers;
use App\Filament\Admin\Resources\Shop\CustomerResource\RelationManagers\AddressesRelationManager;
use App\Filament\Admin\Resources\Shop\CustomerResource\RelationManagers\OrdersRelationManager;
use App\Filament\Admin\Resources\Shop\CustomerResource\Schema\CustomerSchema;
use Domain\Shop\Customer\Enums\CustomerStatus;
use Domain\Shop\Customer\Models\Customer;
use Exception;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Number;
use Override;

class CustomerResource extends Resource
{
    #[\Override]
    protected static ?string $model = Customer::class;

    #[\Override]
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    #[\Override]
    protected static ?int $navigationSort = 2;

    #[\Override]
    protected static ?string $recordTitleAttribute = 'full_name';

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

                Section::make()
                    ->schema(CustomerSchema::schema())
                    ->columns(2)
                    ->columnSpan(['lg' => fn (?Customer $record) => $record === null ? 3 : 2]),

                Section::make()
                    ->schema([

                        TextEntry::make('created_at')
                            ->translateLabel()
                            ->state(fn (Customer $record): ?string => $record->created_at?->diffForHumans()),

                        TextEntry::make('updated_at')
                            ->translateLabel()
                            ->state(fn (Customer $record): ?string => $record->updated_at?->diffForHumans()),
                    ])
                    ->columnSpan(['lg' => 1])
                    ->hiddenOn('create'),
            ])
            ->columns(3);
    }

    /** @throws Exception */
    #[Override]
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('image')
                    ->translateLabel()
                    ->collection('image')
                    ->conversion('thumb')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->circular(),

                TextColumn::make('uuid')
                    ->translateLabel()
                    ->searchable(isIndividual: true)
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),

                TextColumn::make('full_name')
                    ->translateLabel()
                    ->wrap()
                    ->searchable(['first_name', 'last_name'], isIndividual: true)
                    ->sortable(['first_name', 'last_name']),

                TextColumn::make('email')
                    ->translateLabel()
                    ->searchable(isIndividual: true)
                    ->sortable(),

                TextColumn::make('orders_count')
                    ->translateLabel()
                    ->counts('orders')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('status')
                    ->translateLabel()
                    ->badge()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->translateLabel()
                    ->sortable()
                    ->since()
                    ->dateTimeTooltip(),

                TextColumn::make('created_at')
                    ->translateLabel()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
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
                    ->options(CustomerStatus::class),

                TrashedFilter::make()
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
                ]),
            ])
            ->defaultSort('updated_at', 'desc')
            ->groups([
                'status',
                Group::make('created_at')
                    ->collapsible()
                    ->date(),
                Group::make('updated_at')
                    ->collapsible()
                    ->date(),
            ]);
    }

    #[Override]
    public static function getGloballySearchableAttributes(): array
    {
        return [
            'email',
            'first_name',
            'last_name',
            'mobile',
        ];
    }

    #[Override]
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        /** @var Customer $record */
        return [
            'Order count' => (string) Number::format($record->loadCount('orders')->orders_count ?? 0),
        ];
    }

    #[Override]
    public static function getRelations(): array
    {
        return [
            AddressesRelationManager::class,
            OrdersRelationManager::class,
            ActivitiesRelationManager::class,
        ];
    }

    #[Override]
    public static function getPages(): array
    {
        return [
            'index' => ListCustomers::route('/'),
            'create' => CreateCustomer::route('/create'),
            'edit' => EditCustomer::route('/{record}/edit'),
        ];
    }

    #[Override]
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ])
            ->with('media')
            ->withCount('orders');
    }
}
