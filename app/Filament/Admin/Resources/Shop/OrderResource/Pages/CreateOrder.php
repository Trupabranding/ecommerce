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

namespace App\Filament\Admin\Resources\Shop\OrderResource\Pages;

use App\Filament\Admin\Resources\Shop\CustomerResource\Schema\CustomerSchema;
use App\Filament\Admin\Resources\Shop\OrderResource;
use App\Filament\Admin\Resources\Shop\OrderResource\Support;
use App\Filament\Admin\Support\TenantHelper;
use App\Settings\OrderSettings;
use Domain\Shop\Branch\Enums\BranchStatus;
use Domain\Shop\Branch\Models\Branch;
use Domain\Shop\Branch\Models\EloquentBuilder\BranchEloquentBuilder;
use Domain\Shop\Customer\Models\Customer;
use Domain\Shop\Order\Actions\OrderCreatedPipelineAction;
use Domain\Shop\Order\Enums\ClaimType;
use Domain\Shop\Order\Enums\OrderPaymentMethod;
use Domain\Shop\Order\Enums\OrderPaymentStatus;
use Domain\Shop\Order\Enums\OrderStatus;
use Domain\Shop\Order\Models\Order;
use Domain\Shop\Order\Models\OrderItem;
use Domain\Shop\Product\Models\Sku;
use Domain\Shop\Stock\Rules\CheckQuantitySkuStockRule;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Number;
use Override;
use Throwable;

/**
 * @property-read Order $record
 */
class CreateOrder extends CreateRecord
{
    #[\Override]
    protected static string $resource = OrderResource::class;

    #[Override]
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['delivery_price'] = 0; // TODO: delivery price from selected customer address
        $data['total_price'] = 0;

        return $data;
    }

    /** @throws Throwable */
    protected function afterCreate(): void
    {
        app(OrderCreatedPipelineAction::class)
            ->execute($this->record);
    }

    #[Override]
    public function getFormActions(): array
    {
        return [];
    }

    #[Override]
    public function form(Schema $schema): Schema
    {
        $tenantBranch = TenantHelper::getBranch();
        $branchFeatureEnabled = config()->boolean('app-default.branch_feature_enabled');

        return $schema->components([
            Group::make()
                ->schema([
                    Wizard::make([
                        Step::make(trans('Customer Info'))
                            ->schema([
                                Select::make('customer_uuid')
                                    ->translateLabel()
                                    ->nullable()
                                    ->searchable()
                                    ->preload()
                                    ->optionsLimit(20)
                                    ->getOptionLabelFromRecordUsing(
                                        fn (Customer $record) => $record->full_name
                                    )
                                    ->relationship(
                                        'customer',
                                        'full_name',
                                        fn (Builder $query) => $query->latest()
                                    )
                                    ->createOptionForm([
                                        Section::make(
                                            CustomerSchema::schema(),
                                        )->columns(['sm' => 2]),
                                    ])
                                    ->default(function () {
                                        $customerRouteKey = Request::query('customer');

                                        if ($customerRouteKey === null) {
                                            return null;
                                        }

                                        return Customer::where((new Customer)->getRouteKeyName(), $customerRouteKey)
                                            ->value((new Customer)->getKeyName());
                                    }),

                                Select::make('branch_uuid')
                                    ->translateLabel()
                                    ->relationship(
                                        'branch',
                                        'name',
                                        fn (BranchEloquentBuilder $query) => $query
                                            ->where('status', BranchStatus::enabled)
                                            ->when($tenantBranch, fn (BranchEloquentBuilder $query, Branch $branch) => $query->whereKey($branch))
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->visible($branchFeatureEnabled)
                                    ->reactive()
                                    ->disabled($tenantBranch !== null)
                                    ->default($tenantBranch?->getKey() ?? function () use ($branchFeatureEnabled): ?string {
                                        if (! $branchFeatureEnabled) {
                                            return Branch::where('status', BranchStatus::enabled)->value('uuid')
                                                ?? Branch::value('uuid');
                                        }

                                        if (Branch::count() === 1) {

                                            return Branch::value('uuid');
                                        }

                                        return null;
                                    }),

                                DateTimePicker::make('purchased_at')
                                    ->translateLabel()
                                    ->required()
                                    ->default(now()),
                            ]),

                        Step::make(trans('Claim'))
                            ->schema([

                                Select::make('claim_type')
                                    ->translateLabel()
                                    ->options(ClaimType::class)
                                    ->enum(ClaimType::class)
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function (Set $set, ?ClaimType $state) {
                                        if ($state === ClaimType::pickup) {
                                            $set('claim_at', null);
                                        }
                                    }),

                                DateTimePicker::make('claim_at')
                                    ->translateLabel()
                                    ->required()
                                    ->minDate(fn () => now(filament_admin()->timezone))
                                    ->maxDate(function (Get $get) {

                                        $orderSetting = app(OrderSettings::class);
                                        /** @var Branch $branch */
                                        $branch = Branch::find($get('branch_uuid'));

                                        $maxDays = $branch->maximum_advance_booking_days ?? $orderSetting->maximum_advance_booking_days;

                                        if ($maxDays === 0) {
                                            return null;
                                        }

                                        return now(filament_admin()->timezone)->addDays($maxDays);
                                    })
                                    ->native(false)
                                    ->seconds(false)
                                    ->weekStartsOnSunday()
                                    ->disabled(fn (Get $get): bool => blank($get('claim_type')))
                                    ->disabledDates(function (Get $get): array {

                                        if (blank($get('claim_type'))) {
                                            return [];
                                        }

                                        $orderSetting = app(OrderSettings::class);

                                        /** @var ClaimType $claimType */
                                        $claimType = $get('claim_type');
                                        /** @var Branch $branch */
                                        $branch = Branch::find($get('branch_uuid'));

                                        $openingHours = Support::openingHours($branch, $claimType);

                                        $maxDays = $branch->maximum_advance_booking_days ?? $orderSetting->maximum_advance_booking_days;

                                        $disabledDates = [];

                                        foreach (range(1, $maxDays) as $day) {

                                            $date = now(filament_admin()->timezone)->addDays($day);
                                            if ($openingHours->isClosedAt($date)) {
                                                $disabledDates[] = $date;
                                            }
                                        }

                                        return $disabledDates;
                                    })
                                    ->rule(
                                        fn (Get $get): callable => function (
                                            string $attribute,
                                            string $value,
                                            callable $fail
                                        ) use ($get) {

                                            $datetime = now()->parse($value);

                                            /** @var ClaimType $claimType */
                                            $claimType = $get('claim_type');
                                            /** @var Branch $branch */
                                            $branch = Branch::find($get('branch_uuid'));

                                            $openingHours = Support::openingHours($branch, $claimType);

                                            if ($openingHours->isClosedAt($datetime)) {
                                                $fail(trans(':Claim_type claim [:datetime] in not available.', [
                                                    'claim_type' => $claimType->getLabel(),
                                                    'datetime' => $datetime->format('M d, Y h:i A'),
                                                ]));
                                            }
                                        }
                                    )

                                    ->helperText(function (Get $get): ?string {
                                        if (! config()->boolean('app-default.branch_feature_enabled')) {
                                            return null;
                                        }

                                        if ($get('branch_uuid') === null) {
                                            return null;
                                        }

                                        /** @var Branch $branch */
                                        $branch = Branch::find($get('branch_uuid'));

                                        return trans('Base on branch [:branch] available schedule', [
                                            'branch' => $branch->name,
                                        ]);
                                    }),

                            ]),

                        Step::make(trans('Status'))
                            ->schema([

                                Select::make('payment_status')
                                    ->translateLabel()
                                    ->options(OrderPaymentStatus::class)
                                    ->enum(OrderPaymentStatus::class)
                                    ->required()
                                    ->default(OrderPaymentStatus::pending),

                                Select::make('status')
                                    ->translateLabel()
                                    ->options(OrderStatus::class)
                                    ->enum(OrderStatus::class)
                                    ->required()
                                    ->default(OrderStatus::pending),

                                Select::make('payment_method')
                                    ->translateLabel()
                                    ->options(OrderPaymentMethod::class)
                                    ->enum(OrderPaymentMethod::class)
                                    ->nullable(),
                            ]),

                        Step::make(trans('Notes'))
                            ->schema([

                                Textarea::make('notes')
                                    ->translateLabel()
                                    ->nullable()
                                    ->columnSpanFull(),

                            ])
                            ->columns(),

                        Step::make(trans('Order Items'))
                            ->schema([
                                Repeater::make('orderItems')
                                    ->translateLabel()
                                    ->required()
                                    ->relationship('orderItems')
                                    ->maxItems(Sku::count())
                                    ->columns(4)
                                    ->schema(fn () => [
                                        Select::make('sku_uuid')
                                            ->translateLabel()
                                            ->relationship(
                                                'sku',
                                                'code',
                                                fn (Get $get, Builder $query): Builder => $query
                                                    ->whereRelation(
                                                        'skuStocks.branch',
                                                        'uuid',
                                                        $tenantBranch?->getKey() ?? $get('../../branch_uuid')
                                                    )
                                            )
                                            ->searchable()
                                            ->preload()
                                            ->optionsLimit(10)
                                            ->required()
                                            ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                            ->afterStateHydrated(
                                                function (Set $set, ?int $state, ?OrderItem $record): void {

                                                    if ($record !== null) {
                                                        return;
                                                    }

                                                    $price = Sku::whereKey($state)->value('price');
                                                    $set('price', number_format($price / 100, 2));
                                                }
                                            )
                                            ->afterStateUpdated(
                                                function (Set $set, string $state, ?OrderItem $record): void {

                                                    if ($record !== null) {
                                                        return;
                                                    }

                                                    $sku = Sku::whereKey($state)->first();

                                                    if ($sku === null) {
                                                        return;
                                                    }

                                                    $set('price', $sku->price);
                                                    $set('minimum_type', $sku->minimum_type);
                                                    $set('minimum', $sku->minimum);
                                                    $set('maximum', $sku->maximum);
                                                    $set('quantity', $sku->minimum ?? 1);
                                                }
                                            )
                                            ->reactive(),

                                        TextInput::make('price')
                                            ->translateLabel()
                                            ->numeric()
                                            ->money()
                                            ->disabled()
                                            ->dehydrated(false),

                                        Group::make()
                                            ->columns()
                                            ->schema([
                                                TextInput::make('minimum')
                                                    ->translateLabel()
                                                    ->disabled()
                                                    ->dehydrated(false),

                                                TextInput::make('maximum')
                                                    ->translateLabel()
                                                    ->disabled()
                                                    ->dehydrated(false),
                                            ]),

                                        TextInput::make('quantity')
                                            ->translateLabel()
                                            ->required()
                                            ->numeric()
                                            ->minValue(fn (Get $get) => $get('minimum') ?? 1)
                                            ->maxValue(fn (Get $get) => $get('maximum') ?? 1)
                                            ->rule(
                                                function (Get $get) use ($tenantBranch) {

                                                    /** @var Branch $branch */
                                                    $branch = $tenantBranch ?? Branch::whereKey($get('../../branch_uuid'))->first();

                                                    return new CheckQuantitySkuStockRule(
                                                        branch: $branch,
                                                        sku: $get('sku_uuid'),
                                                    );
                                                },
                                                // prevent the rule from running when the sku_uuid is null
                                                fn (Get $get) => $get('sku_uuid') !== null
                                            )
                                            ->disabled(fn (?int $state) => $state === null)
                                            ->reactive(),

                                        //            Forms\Components\TextInput::make('total')
                                        //                ->translateLabel()
                                        //                ->visibleOn('view')
                                        //                ->formatStateUsing(fn (?OrderItem $record) => $record === null
                                        //                    ? null
                                        //                    : number_format($record->total_price / 100, 2)),
                                    ]),
                            ]),
                    ])
                        ->submitAction($this->getSubmitFormAction())
                        ->cancelAction($this->getCancelFormAction()),
                ])
                ->columnSpan(['lg' => 3]),

            Section::make()
                ->schema([
                    TextEntry::make('total_price_placeholder')
                        ->label('Total price')
                        ->translateLabel()
                        ->state(
                            fn (Get $get) => Number::currency(
                                Support::callCalculatorForTotalPrice($get('orderItems')),
                                config()->string('app-default.currency')
                            )
                        ),
                ])
                ->columnSpan(['lg' => 1]),

        ])
            ->columns(4);
    }
}
