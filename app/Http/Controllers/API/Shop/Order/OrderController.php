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

namespace App\Http\Controllers\API\Shop\Order;

use App\Http\Requests\API\Shop\Order\OrderRequest;
use App\Http\Resources\Shop\OrderResource;
use Domain\Shop\Branch\Models\Branch;
use Domain\Shop\Order\Actions\CreateOrderAction;
use Domain\Shop\Order\DataTransferObjects\OrderData;
use Domain\Shop\Order\Enums\ClaimType;
use Domain\Shop\Order\Enums\OrderPaymentMethod;
use Domain\Shop\Order\Models\Order;
use Illuminate\Support\Facades\DB;
use Spatie\RouteAttributes\Attributes\Middleware;
use Spatie\RouteAttributes\Attributes\Post;
use Throwable;

/**
 * @tags Order
 */
#[Middleware('auth:sanctum')]
class OrderController
{
    /** @throws Throwable */
    #[Post('branches/{enabledBranch}/orders', 'orders.store')]
    public function __invoke(OrderRequest $request, Branch $enabledBranch): OrderResource
    {
        $customer = customer_auth();

        $data = $request->validated();

        $order = DB::transaction(
            fn () => app(CreateOrderAction::class)
                ->execute(new OrderData(
                    branch: $enabledBranch,
                    customer: $customer,
                    payment_method: OrderPaymentMethod::from($data['payment_method']),
                    claimType: ClaimType::from($data['claim_type']),
                    claim_at: now()->parse($data['claim_at'])->timezone($customer->timezone),
                    notes: $data['notes'] ?? null,
                ))
        );

        return OrderResource::make($order);
    }
}
