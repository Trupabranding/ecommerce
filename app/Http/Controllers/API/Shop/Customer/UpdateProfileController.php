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

namespace App\Http\Controllers\API\Shop\Customer;

use App\Http\Requests\API\Shop\Customer\UpdateProfileRequest;
use App\Http\Resources\Shop\CustomerResource;
use Domain\Shop\Customer\Actions\EditCustomerRegisterAction;
use Domain\Shop\Customer\Models\Customer;
use Illuminate\Support\Facades\DB;
use Spatie\RouteAttributes\Attributes\Middleware;
use Spatie\RouteAttributes\Attributes\Prefix;
use Spatie\RouteAttributes\Attributes\Put;
use Throwable;

/**
 * @tags Customer
 */
#[Prefix('customers'), Middleware('auth:sanctum')]
class UpdateProfileController
{
    /**
     * Update profile
     *
     * @throws Throwable
     */
    #[Put('profile', name: 'customers.profile.update')]
    public function __invoke(UpdateProfileRequest $request): CustomerResource
    {
        $customer = customer_auth();

        DB::transaction(fn () => app(EditCustomerRegisterAction::class)
            ->execute($customer, $request->toDTO()));

        return CustomerResource::make($customer->refresh());
    }
}
