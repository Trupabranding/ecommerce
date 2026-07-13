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

namespace App\Http\Controllers\API\Shop\Cart;

use App\Http\Requests\API\Shop\Cart\CartEditRequest;
use App\Http\Requests\API\Shop\Cart\CartStoreRequest;
use App\Http\Resources\Shop\CartResource;
use Domain\Shop\Branch\Models\Branch;
use Domain\Shop\Cart\Actions\CreateCartAction;
use Domain\Shop\Cart\Actions\DeleteCartAction;
use Domain\Shop\Cart\Actions\EditCartAction;
use Domain\Shop\Cart\DataTransferObjects\CreateCartData;
use Domain\Shop\Cart\DataTransferObjects\EditCartData;
use Domain\Shop\Cart\Models\Cart;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\RouteAttributes\Attributes\Delete;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Middleware;
use Spatie\RouteAttributes\Attributes\Post;
use Spatie\RouteAttributes\Attributes\Prefix;
use Spatie\RouteAttributes\Attributes\Put;
use Spatie\RouteAttributes\Attributes\ScopeBindings;
use Throwable;

#[Prefix('branches/{enabledBranch}/carts'), Middleware('auth:sanctum')]
class CartController
{
    #[Get('/', name: 'carts.index')]
    public function index(Branch $enabledBranch): mixed
    {
        return CartResource::collection(
            QueryBuilder::for(
                Cart::whereBelongsTo(customer_auth())
                    ->whereBelongsTo($enabledBranch)
            )
                ->allowedFilters(['sku_uuid'])
                ->allowedSorts(['sku_uuid', 'quantity'])
                ->defaultSort('updated_at')
                ->allowedIncludes(['sku.product'])
                ->jsonPaginate()
        );
    }

    /**
     * @throws Throwable
     */
    #[Post('/', name: 'carts.store')]
    public function store(CartStoreRequest $request, Branch $enabledBranch): mixed
    {
        $cart = DB::transaction(fn () => app(CreateCartAction::class)
            ->execute(new CreateCartData(
                branch: $enabledBranch,
                customer: customer_auth(),
                sku_uuid: (string) $request->string('sku_uuid'),
                quantity: $request->float('quantity'),
            )));

        return CartResource::make($cart);
    }

    /**
     * @throws Throwable
     */
    #[Put('{cart}', name: 'carts.update')]
    #[ScopeBindings]
    public function update(CartEditRequest $request, Branch $enabledBranch, Cart $cart): mixed
    {
        Gate::authorize('update', $cart);

        DB::transaction(fn () => app(EditCartAction::class)
            ->execute($cart, new EditCartData(
                quantity: (float) $request->input('quantity'),
            )));

        return CartResource::make($cart->refresh());
    }

    #[Delete('/empty', name: 'carts.empty')]
    public function empty(Branch $enabledBranch): mixed
    {
        DB::transaction(fn () => customer_auth()
            ->carts()
            ->where('branch_uuid', $enabledBranch->getKey())
            ->delete());

        return response()->noContent();
    }

    /**
     * @throws Throwable
     */
    #[Delete('{cart}', name: 'carts.destroy')]
    #[ScopeBindings]
    public function destroy(Branch $enabledBranch, Cart $cart): mixed
    {
        Gate::authorize('delete', $cart);

        DB::transaction(fn () => app(DeleteCartAction::class)->execute($cart));

        return response()->noContent();
    }
}
