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

namespace App\Http\Middleware;

use Closure;
use Domain\Access\Admin\Models\Admin;
use Domain\Shop\Customer\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Sentry\State\HubInterface;
use Sentry\State\Scope;
use Sentry\UserDataBag;
use Symfony\Component\HttpFoundation\Response;

use function Sentry\configureScope;

class SentryUserContext
{
    private bool $sentryEnabled;

    public function __construct()
    {
        $this->sentryEnabled = app(HubInterface::class)->getClient()?->getOptions()->getDsn() !== null;
    }

    /**
     * @param  Closure(Request):Response  $next
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if (! $this->sentryEnabled) {
            return $next($request);
        }

        if (Auth::check()) {

            /** @var Admin|Customer $user */
            /** @phpstan-ignore varTag.type */
            $user = Auth::user();

            if ($user instanceof Admin) {
                self::admin($user, $request);
            } elseif ($user instanceof Customer) {
                self::customer($user, $request);
            }

        }

        return $next($request);
    }

    private static function customer(Customer $customer, Request $request): void
    {
        configureScope(function (Scope $scope) use ($request, $customer): void {

            $userDataBag = new UserDataBag(
                id: $customer->getKey(),
                email: $customer->email,
                ipAddress: $request->ip(),
            );

            $scope->setUser(
                $userDataBag
                    ->setMetadata('user_model_type', 'customer')
            );
        });
    }

    public static function admin(Admin $admin, Request $request): void
    {
        configureScope(function (Scope $scope) use ($request, $admin): void {

            $userDataBag = new UserDataBag(
                id: $admin->getKey(),
                email: $admin->email,
                ipAddress: $request->ip(),
            );

            $scope->setUser(
                $userDataBag
                    ->setMetadata('user_model_type', 'admin')
            );
        });
    }
}
