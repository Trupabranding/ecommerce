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

namespace App\Http\Controllers\API\Concern;

use App\Exceptions\RateLimitExceedException;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Request;

trait RateLimit
{
    protected function clearRateLimiter(): void
    {
        RateLimiter::clear($this->getRateLimitKey());
    }

    protected function getRateLimitKey(): string
    {
        /** @var array{file:string,class:string,function:string} $trace */
        $trace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 3)[2];

        // https://github.com/laravel/framework/blob/11.x/src/Illuminate/Support/Onceable.php#L69

        $plainKey = sprintf(
            '%s@%s%s (%s)',
            $trace['file'],
            $trace['class'].'@',
            $trace['function'],
            Request::ip(),
        );

        //        ray($plainKey);

        return 'rate-limit:'.sha1($plainKey);
    }

    /**
     * @throws RateLimitExceedException
     */
    protected function rateLimit(int $maxAttempts = 5, int $decaySeconds = 60, ?string $errorMessage = null): void
    {
        $key = $this->getRateLimitKey();

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $secondsUntilAvailable = RateLimiter::availableIn($key);

            throw new RateLimitExceedException($secondsUntilAvailable, $errorMessage);
        }

        RateLimiter::hit($key, $decaySeconds);
    }
}
