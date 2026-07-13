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

use App\Jobs\QueueName;
use App\Settings\OrderSettings;
use App\Settings\SiteSettings;
use Domain\Access\Admin\Models\Admin;
use Domain\Shop\Order\Enums\OrderPaymentMethod;
use Illuminate\Support\Facades\Artisan;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Config\PermissionConfig;

Artisan::command('app:horizon:clear', function () {
    /** @var Illuminate\Foundation\Console\ClosureCommand $this */
    foreach (QueueName::cases() as $queueName) {
        Artisan::call('horizon:clear', ['--queue' => $queueName->value, '--force' => true]);
        $this->info(Artisan::output());
    }

    $this->info('Done clear jobs on horizon..');
});

Artisan::command('app:bootstrap-customer
    {--profile= : Path to JSON bootstrap profile file}
    {--owner-name= : Owner full name}
    {--owner-email= : Owner email}
    {--owner-password-hash= : Bcrypt hashed owner password}
    {--site-name= : Brand/site name}
    {--legal-name= : Legal business name}
    {--support-email= : Customer support email}
    {--support-phone= : Customer support phone}
    {--website-url= : Public website URL}
    {--address= : Company address}
    {--tax-number= : Tax/VAT number}
    {--registration-number= : Company registration number}
    {--invoice-footer= : Invoice footer/legal text}
    {--timezone= : Timezone, e.g. Asia/Manila}
    {--locale= : Locale code, e.g. en or en_US}
    {--currency= : ISO 4217 currency code}
    {--order-prefix= : Order number prefix}
    {--auto-cancel-unpaid-minutes= : Minutes to auto-cancel unpaid orders (0 disables)}
    {--daily-order-limit= : Max daily orders (0 means unlimited)}
    {--allow-guest-checkout= : 1 to allow guest checkout, 0 to disable}
    {--allowed-payment-methods= : Comma-separated allowed payment methods}
    {--default-payment-method= : Default payment method} ', function () {
    /** @var Illuminate\Foundation\Console\ClosureCommand $this */

    $profile = [];
    $profilePath = $this->option('profile');

    if (filled($profilePath)) {
        $resolvedProfilePath = str_starts_with((string) $profilePath, '/')
            ? (string) $profilePath
            : base_path((string) $profilePath);

        if (! is_file($resolvedProfilePath)) {
            $this->error('Profile file not found: '.$resolvedProfilePath);

            return;
        }

        try {
            $profileData = json_decode((string) file_get_contents($resolvedProfilePath), true, flags: JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            $this->error('Invalid JSON profile: '.$e->getMessage());

            return;
        }

        if (! is_array($profileData)) {
            $this->error('Profile JSON must decode to an object.');

            return;
        }

        $profile = $profileData;
    }

    $resolveOption = function (string $optionName, string $profileKey, mixed $fallback = null) use ($profile) {
        $optionValue = $this->option($optionName);

        if ($optionValue !== null && $optionValue !== '') {
            return $optionValue;
        }

        if (array_key_exists($profileKey, $profile) && $profile[$profileKey] !== '') {
            return $profile[$profileKey];
        }

        return $fallback;
    };

    $ownerName = (string) $resolveOption('owner-name', 'owner_name', config()->string('seeder.admin_name'));
    $ownerEmail = (string) $resolveOption('owner-email', 'owner_email', config()->string('seeder.admin_email'));
    $ownerPasswordHash = (string) $resolveOption('owner-password-hash', 'owner_password_hash', config()->string('seeder.admin_hash_password'));

    if (blank($ownerEmail)) {
        $this->error('Owner email is required. Use --owner-email or set SUPER_ADMIN_EMAIL.');

        return;
    }

    if (blank($ownerPasswordHash)) {
        $this->error('Owner password hash is required. Use --owner-password-hash or set SUPER_ADMIN_PASSWORD_HASH.');

        return;
    }

    $allowedPaymentMethodsRaw = $resolveOption('allowed-payment-methods', 'allowed_payment_methods', ['cash', 'g_cash']);

    $allowedPaymentMethods = collect(is_array($allowedPaymentMethodsRaw)
        ? $allowedPaymentMethodsRaw
        : explode(',', (string) $allowedPaymentMethodsRaw))
            ->map(fn (string $value): string => trim($value))
            ->filter()
            ->values();

    $validPaymentMethods = collect(OrderPaymentMethod::cases())->map(fn (OrderPaymentMethod $method): string => $method->value);

    if ($allowedPaymentMethods->isEmpty()) {
        $this->error('At least one payment method must be allowed.');

        return;
    }

    $invalidPaymentMethods = $allowedPaymentMethods->diff($validPaymentMethods)->values();

    if ($invalidPaymentMethods->isNotEmpty()) {
        $this->error('Invalid payment methods: '.$invalidPaymentMethods->implode(', '));
        $this->line('Valid methods: '.$validPaymentMethods->implode(', '));

        return;
    }

    $defaultPaymentMethod = (string) $resolveOption('default-payment-method', 'default_payment_method', 'cash');

    if (! $allowedPaymentMethods->contains($defaultPaymentMethod)) {
        $this->error('Default payment method must be included in allowed payment methods.');

        return;
    }

    $owner = Admin::query()->firstOrCreate(
        ['email' => $ownerEmail],
        [
            'name' => $ownerName,
            'password' => $ownerPasswordHash,
        ],
    );
    $owner->assignRole(PermissionConfig::superAdmin());

    /** @var SiteSettings $site */
    $site = app(SiteSettings::class);
    $site->name = (string) $resolveOption('site-name', 'site_name', $site->name);
    $site->legal_name = $resolveOption('legal-name', 'legal_name', $site->legal_name);
    $site->support_email = $resolveOption('support-email', 'support_email', $site->support_email);
    $site->support_phone = $resolveOption('support-phone', 'support_phone', $site->support_phone);
    $site->website_url = $resolveOption('website-url', 'website_url', $site->website_url);
    $site->address = $resolveOption('address', 'address', $site->address);
    $site->tax_number = $resolveOption('tax-number', 'tax_number', $site->tax_number);
    $site->registration_number = $resolveOption('registration-number', 'registration_number', $site->registration_number);
    $site->invoice_footer = $resolveOption('invoice-footer', 'invoice_footer', $site->invoice_footer);
    $site->timezone = (string) $resolveOption('timezone', 'timezone', $site->timezone);
    $site->locale = (string) $resolveOption('locale', 'locale', $site->locale);
    $site->currency = strtoupper((string) $resolveOption('currency', 'currency', $site->currency));
    $site->save();

    /** @var OrderSettings $order */
    $order = app(OrderSettings::class);
    $order->prefix = strtoupper((string) $resolveOption('order-prefix', 'order_prefix', $order->prefix));
    $order->auto_cancel_unpaid_minutes = (int) $resolveOption('auto-cancel-unpaid-minutes', 'auto_cancel_unpaid_minutes', $order->auto_cancel_unpaid_minutes);
    $order->daily_order_limit = (int) $resolveOption('daily-order-limit', 'daily_order_limit', $order->daily_order_limit);
    $order->allow_guest_checkout = (bool) ((int) $resolveOption('allow-guest-checkout', 'allow_guest_checkout', (int) $order->allow_guest_checkout));
    $order->allowed_payment_methods = $allowedPaymentMethods->all();
    $order->default_payment_method = $defaultPaymentMethod;
    $order->save();

    $this->info('Customer bootstrap complete.');
    $this->line('Owner: '.$owner->email);
    $this->line('Site: '.$site->name.' | '.$site->currency.' | '.$site->timezone);
    $this->line('Order: prefix='.$order->prefix.', default-payment='.$order->default_payment_method);
});
