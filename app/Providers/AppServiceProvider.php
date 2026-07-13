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

namespace App\Providers;

use App\Providers\ActivitylogLoggablePipes\MoneyFromLogChangesPipe;
use App\Providers\ActivitylogLoggablePipes\RedactHiddenAttributesFromLogChangesPipe;
use App\Providers\Macros\BluePrintMixin;
use Domain\Access\Admin\Models\Admin;
use Domain\Settings\Models\AdminSetting;
use Domain\Settings\Models\SettingCategory;
use Domain\Settings\Models\SettingFeature;
use Domain\Settings\Models\SettingValue;
use Domain\Shop\Branch\Models\Branch;
use Domain\Shop\Brand\Models\Brand;
use Domain\Shop\Cart\Models\Cart;
use Domain\Shop\Category\Models\Category;
use Domain\Shop\Customer\Models\Address;
use Domain\Shop\Customer\Models\Customer;
use Domain\Shop\OperationHour\Models\OperationHour;
use Domain\Shop\Order\Models\Order;
use Domain\Shop\Order\Models\OrderInvoice;
use Domain\Shop\Order\Models\OrderItem;
use Domain\Shop\Product\Models\Attribute;
use Domain\Shop\Product\Models\AttributeOption;
use Domain\Shop\Product\Models\Product;
use Domain\Shop\Product\Models\Sku;
use Domain\Shop\Stock\Models\SkuStock;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;
use Illuminate\Http\Request;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Number;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Email;
use Illuminate\Validation\Rules\Password;
use Laravel\Telescope\TelescopeServiceProvider as TelescopeServiceProviderVendor;
use ReflectionException;
use Sentry\Laravel\Integration;
use Sentry\State\HubInterface;
use TiMacDonald\JsonApi\JsonApiResource;

class AppServiceProvider extends ServiceProvider
{
    /** @throws ReflectionException */
    public function boot(): void
    {
        Model::shouldBeStrict();
        Model::automaticallyEagerLoadRelationships();
        DB::prohibitDestructiveCommands($this->app->isProduction());

        if ($this->app->environment('production', 'staging')) {

            if (app(HubInterface::class)->getClient()?->getOptions()->getDsn() !== null) {
                Model::handleLazyLoadingViolationUsing(
                    Integration::lazyLoadingViolationReporter()
                );
                Model::handleDiscardedAttributeViolationUsing(
                    Integration::discardedAttributeViolationReporter()
                );
                Model::handleMissingAttributeViolationUsing(
                    Integration::missingAttributeViolationReporter()
                );
            } else {
                Model::handleLazyLoadingViolationUsing(function (Model $model, string $relation) {
                    $class = $model::class;

                    Log::info("Attempted to lazy load [{$relation}] on model [{$class}].");
                });
                Model::handleDiscardedAttributeViolationUsing(function (Model $model, array $attributes) {

                    /** @var non-empty-list<string> $attributes */
                    $class = $model::class;

                    Log::info("Attempted to discard attribute (try to add fillable) [{$attributes[0]}] on model [{$class}].)");
                });
                Model::handleMissingAttributeViolationUsing(function (Model $model, string $attribute) {
                    $class = $model::class;

                    Log::info("Attempted to access missing attribute [{$attribute}] on model [{$class}].");
                });
            }
        }

        /** @phpstan-ignore argument.type */
        Relation::enforceMorphMap([
            Admin::class,
            Product::class,
            Customer::class,
            Order::class,
            OrderItem::class,
            OrderInvoice::class,
            Sku::class,
            Attribute::class,
            AttributeOption::class,
            Branch::class,
            Brand::class,
            Address::class,
            Category::class,
            SkuStock::class,
            Cart::class,
            OperationHour::class,
            AdminSetting::class,
            SettingCategory::class,
            SettingFeature::class,
            SettingValue::class,
            config()->string('permission.models.role'),
            config()->string('permission.models.permission'),
        ]);

        Password::defaults(
            fn () => $this->app->environment('local', 'testing')
                ? Password::min(4)
                : Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised()
        );

        Email::defaults(
            fn () => $this->app->environment('local', 'testing')
                ? Rule::email()
                : Rule::email()
                    ->rfcCompliant()
                    ->validateMxRecord()
        );

        if (class_exists(TelescopeServiceProviderVendor::class)) {
            $this->app->register(TelescopeServiceProviderVendor::class);
            $this->app->register(TelescopeServiceProvider::class);
        }

        JsonApiResource::resolveIdUsing(fn (Model $model): string => (string) $model->getRouteKey());

        // https://laravel.com/docs/10.x/localization#handling-missing-translation-strings
        // Lang::handleMissingKeysUsing(fn (string $key) => Log::info('Missing translation key .'.$key));

        Builder::$defaultMorphKeyType = 'uuid';

        Number::useCurrency(config()->string('app-default.currency'));

        ThrottleRequests::shouldHashKeys(false);
        RateLimiter::for(
            'api',
            fn (Request $request) => Limit::perMinute(60)
                ->by($request->user()?->getKey() ?? $request->ip())
        );

        $this->macros();
        $this->bootReloadCommands();
        $this->activityLogPipe();
    }

    private function bootReloadCommands(): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->reloads('schedule:clear-cache', 'schedule:clear-cache');
        $this->reloads('schedule:interrupt', 'schedule:interrupt');
        $this->reloads('schedule-monitor:sync', 'schedule-monitor:sync');
        $this->reloads('permission:sync', 'permission:sync');
    }

    /** @throws ReflectionException */
    private function macros(): void
    {
        if ($this->app->runningInConsole()) {
            Blueprint::mixin(new BluePrintMixin);
        }
    }

    private function activityLogPipe(): void
    {
        Admin::addLogChange(new RedactHiddenAttributesFromLogChangesPipe);
        Customer::addLogChange(new RedactHiddenAttributesFromLogChangesPipe);

        Cart::addLogChange(new MoneyFromLogChangesPipe);
        Order::addLogChange(new MoneyFromLogChangesPipe);
        OrderItem::addLogChange(new MoneyFromLogChangesPipe);
        Sku::addLogChange(new MoneyFromLogChangesPipe);
    }
}
