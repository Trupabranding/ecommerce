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

namespace App\Filament\Admin\Resources\Shop\CustomerResource\RelationManagers;

use App\Filament\Admin\Resources\Shop\OrderResource;
use App\Filament\Admin\Resources\Shop\OrderResource\Pages\ViewOrder;
use Domain\Shop\Customer\Models\Customer;
use Domain\Shop\Order\Enums\OrderStatus;
use Domain\Shop\Order\Models\EloquentBuilder\OrderEloquentBuilder;
use Exception;
use Filament\Actions\Action;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Override;

class OrdersRelationManager extends RelationManager
{
    #[\Override]
    protected static string $relationship = 'orders';

    #[\Override]
    protected static ?string $recordTitleAttribute = 'receipt_number';

    #[Override]
    public static function getBadgeTooltip(Model $ownerRecord, string $pageClass): ?string
    {
        return trans('There are new pending orders.');
    }

    #[Override]
    public static function getBadgeColor(Model $ownerRecord, string $pageClass): ?string
    {
        return 'warning';
    }

    #[Override]
    public static function getBadge(Model $ownerRecord, string $pageClass): ?string
    {
        /** @var Customer $ownerRecord */
        $ordersCount = $ownerRecord->loadCount([
            'orders' => fn (OrderEloquentBuilder $query) => $query
                ->where('status', OrderStatus::pending),
        ])->orders_count;

        if ($ordersCount === 0) {
            return null;
        }

        return (string) $ordersCount;
    }

    #[Override]
    public function infolist(Schema $schema): Schema
    {
        return ViewOrder::staticInfolist($schema);
    }

    /** @throws Exception */
    #[Override]
    public function table(Table $table): Table
    {
        return OrderResource::table($table)
            ->headerActions([
                Action::make('new_order')
                    ->translateLabel()
                    ->url(
                        OrderResource::can('create')
                         ? OrderResource::getUrl('create', ['customer' => $this->ownerRecord->getRouteKey()])
                            : null
                    ),
            ]);
    }
}
