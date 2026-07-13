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

namespace App\Providers\ActivitylogLoggablePipes;

use Brick\Money\Money;
use Closure;
use Override;
use Spatie\Activitylog\Contracts\LoggablePipe;
use Spatie\Activitylog\EventLogBag;

class MoneyFromLogChangesPipe implements LoggablePipe
{
    #[Override]
    public function handle(EventLogBag $event, Closure $next): EventLogBag
    {
        if (isset($event->changes['attributes'])) {
            $event->changes['attributes'] = self::format($event->changes['attributes']);
        }

        if (isset($event->changes['old'])) {
            $event->changes['old'] = self::format($event->changes['old']);
        }

        return $next($event);
    }

    private static function format(array $changes): array
    {
        foreach ($changes as $property => $value) {
            if ($value instanceof Money) {
                $changes[$property] = [
                    'amount' => moneyAmountToFloat($value),
                    'currency' => config()->string('app-default.currency'),
                ];
            }
        }

        return $changes;
    }
}
