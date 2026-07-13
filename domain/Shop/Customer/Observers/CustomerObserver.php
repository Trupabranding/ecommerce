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

namespace Domain\Shop\Customer\Observers;

use App\Observers\LogAttemptDeleteResource;
use Domain\Shop\Customer\Models\Customer;
use Domain\Shop\Order\Models\EloquentBuilder\OrderEloquentBuilder;
use Filament\Support\Exceptions\Halt;

class CustomerObserver
{
    use LogAttemptDeleteResource;

    /**
     * @throws Halt
     */
    public function deleting(Customer $customer): void
    {
        $customer->loadCount([
            'orders' => function (OrderEloquentBuilder $query) {
                $query->withTrashed();
            },
        ]);

        if ($customer->orders_count > 0) {

            self::abortThenLogAttemptDeleteRelationCount(
                $customer,
                trans('Can not delete customer with associated orders.'),
                'orders',
                $customer->orders_count
            );

        }
    }
}
