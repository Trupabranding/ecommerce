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

use App\Settings\OrderSettings;
use App\Settings\SiteSettings;
use Domain\Access\Admin\Models\Admin;
use Domain\Shop\Order\Enums\OrderPaymentMethod;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Testing\Fluent\AssertableJson;

use function Pest\Laravel\artisan;

// Set a test password hash environment variable
// Using a pre-computed bcrypt hash for testing
putenv('SUPER_ADMIN_PASSWORD_HASH=$2y$12$HHEqQCF.dJTYBhqPJakGaOPZPc8lBG8qCOz1OjfFpRjDY9RkFe38K');

describe('Bootstrap Customer Command - CLI Options', function () {
    it('validates invalid payment methods', function () {
        Artisan::call('app:bootstrap-customer', [
            '--owner-name' => 'Test Admin',
            '--owner-email' => 'admin@invalid.com',
            '--owner-password-hash' => bcrypt('password123'),
            '--allowed-payment-methods' => 'invalid_method',
            '--default-payment-method' => 'invalid_method',
            '--timezone' => 'UTC',
            '--locale' => 'en',
            '--currency' => 'USD',
        ]);

        expect(Artisan::output())
            ->toContain('Invalid payment methods');
    });

    it('validates default payment method is in allowed methods', function () {
        Artisan::call('app:bootstrap-customer', [
            '--owner-name' => 'Test Admin',
            '--owner-email' => 'admin@test.com',
            '--owner-password-hash' => bcrypt('password123'),
            '--allowed-payment-methods' => 'cash,g_cash',
            '--default-payment-method' => 'credit_card',
            '--timezone' => 'UTC',
            '--locale' => 'en',
            '--currency' => 'USD',
        ]);

        expect(Artisan::output())
            ->toContain('Default payment method must be included in allowed payment methods');
    });

    it('handles comma-separated payment methods', function () {
        Artisan::call('app:bootstrap-customer', [
            '--owner-name' => 'Test Admin',
            '--owner-email' => 'admin@comma.com',
            '--owner-password-hash' => bcrypt('password123'),
            '--allowed-payment-methods' => 'cash, g_cash',
            '--default-payment-method' => 'g_cash',
            '--timezone' => 'UTC',
            '--locale' => 'en',
            '--currency' => 'USD',
        ]);

        expect(Artisan::output())
            ->toContain('Customer bootstrap complete');

        $order = app(OrderSettings::class);
        expect($order->allowed_payment_methods)
            ->toContain('cash')
            ->toContain('g_cash');
    });
});

describe('Bootstrap Customer Command - Profile Option', function () {
    it('can execute with profile option using JSON file', function () {
        $profilePath = storage_path('testing/bootstrap-profile.json');
        File::ensureDirectoryExists(dirname($profilePath));

        $hashPassword = bcrypt('password456');

        $profileData = [
            'owner_name' => 'Profile Owner',
            'owner_email' => 'profile@example.com',
            'owner_password_hash' => $hashPassword,
            'site_name' => 'Profile Store',
            'legal_name' => 'Profile Store LLC',
            'support_email' => 'support@profile.com',
            'timezone' => 'Asia/Manila',
            'locale' => 'fil',
            'currency' => 'PHP',
            'order_prefix' => 'PRF',
            'auto_cancel_unpaid_minutes' => '120',
            'daily_order_limit' => '500',
            'allow_guest_checkout' => true,
            'allowed_payment_methods' => ['cash', 'g_cash'],
            'default_payment_method' => 'cash',
        ];

        File::put($profilePath, json_encode($profileData));

        Artisan::call('app:bootstrap-customer', [
            '--profile' => $profilePath,
        ]);

        expect(Artisan::output())
            ->toContain('Customer bootstrap complete')
            ->toContain('profile@example.com');

        $admin = Admin::query()
            ->where('email', 'profile@example.com')
            ->first();

        expect($admin)
            ->not->toBeNull()
            ->and($admin->name)->toBe('Profile Owner');

        $site = app(SiteSettings::class);
        expect($site->name)->toBe('Profile Store')
            ->and($site->legal_name)->toBe('Profile Store LLC')
            ->and($site->timezone)->toBe('Asia/Manila')
            ->and($site->locale)->toBe('fil');

        $order = app(OrderSettings::class);
        expect($order->prefix)->toBe('PRF')
            ->and($order->auto_cancel_unpaid_minutes)->toBe(120);

        File::delete($profilePath);
    });

    it('validates profile file exists', function () {
        $hashPassword = bcrypt('password123');

        Artisan::call('app:bootstrap-customer', [
            '--profile' => '/nonexistent/path/to/profile.json',
            '--owner-password-hash' => $hashPassword,
        ]);

        expect(Artisan::output())
            ->toContain('Profile file not found');
    });

    it('validates profile JSON is valid', function () {
        $profilePath = storage_path('testing/invalid-profile.json');
        File::ensureDirectoryExists(dirname($profilePath));
        File::put($profilePath, 'invalid json {');

        Artisan::call('app:bootstrap-customer', [
            '--profile' => $profilePath,
        ]);

        expect(Artisan::output())
            ->toContain('Invalid JSON profile');

        File::delete($profilePath);
    });

    it('uses relative profile path correctly', function () {
        $relativePath = 'storage/testing/relative-profile.json';
        $absolutePath = base_path($relativePath);
        File::ensureDirectoryExists(dirname($absolutePath));

        $hashPassword = bcrypt('password789');

        $profileData = [
            'owner_name' => 'Relative Admin',
            'owner_email' => 'relative@example.com',
            'owner_password_hash' => $hashPassword,
            'timezone' => 'UTC',
            'locale' => 'en',
            'currency' => 'USD',
            'allowed_payment_methods' => ['cash'],
            'default_payment_method' => 'cash',
        ];

        File::put($absolutePath, json_encode($profileData));

        Artisan::call('app:bootstrap-customer', [
            '--profile' => $relativePath,
        ]);

        expect(Artisan::output())
            ->toContain('Customer bootstrap complete')
            ->toContain('relative@example.com');

        File::delete($absolutePath);
    });

    it('uses absolute profile path correctly', function () {
        $absolutePath = storage_path('testing/absolute-profile.json');
        File::ensureDirectoryExists(dirname($absolutePath));

        $hashPassword = bcrypt('password321');

        $profileData = [
            'owner_name' => 'Absolute Admin',
            'owner_email' => 'absolute@example.com',
            'owner_password_hash' => $hashPassword,
            'timezone' => 'UTC',
            'locale' => 'en',
            'currency' => 'USD',
            'allowed_payment_methods' => ['cash'],
            'default_payment_method' => 'cash',
        ];

        File::put($absolutePath, json_encode($profileData));

        Artisan::call('app:bootstrap-customer', [
            '--profile' => $absolutePath,
        ]);

        expect(Artisan::output())
            ->toContain('Customer bootstrap complete')
            ->toContain('absolute@example.com');

        File::delete($absolutePath);
    });
});

describe('Bootstrap Customer Command - Option Precedence', function () {
    it('CLI options override profile options', function () {
        $profilePath = storage_path('testing/precedence-profile.json');
        File::ensureDirectoryExists(dirname($profilePath));

        $hashPassword = bcrypt('profilepass');

        $profileData = [
            'owner_name' => 'Profile Name',
            'owner_email' => 'profile@override.com',
            'owner_password_hash' => $hashPassword,
            'site_name' => 'Profile Site',
            'timezone' => 'Asia/Manila',
            'locale' => 'fil',
            'currency' => 'PHP',
            'allowed_payment_methods' => ['cash'],
            'default_payment_method' => 'cash',
        ];

        File::put($profilePath, json_encode($profileData));

        // CLI options should override profile
        Artisan::call('app:bootstrap-customer', [
            '--profile' => $profilePath,
            '--owner-name' => 'CLI Name',
            '--site-name' => 'CLI Site',
            '--timezone' => 'America/New_York',
        ]);

        expect(Artisan::output())
            ->toContain('Customer bootstrap complete');

        $admin = Admin::query()
            ->where('email', 'profile@override.com')
            ->first();

        expect($admin->name)->toBe('CLI Name');

        $site = app(SiteSettings::class);
        expect($site->name)->toBe('CLI Site')
            ->and($site->timezone)->toBe('America/New_York');

        File::delete($profilePath);
    });

    it('profile options override default config values', function () {
        $profilePath = storage_path('testing/defaults-profile.json');
        File::ensureDirectoryExists(dirname($profilePath));

        $hashPassword = bcrypt('defaultpass');

        $profileData = [
            'owner_name' => 'Default Override Admin',
            'owner_email' => 'defaults@override.com',
            'owner_password_hash' => $hashPassword,
            'site_name' => 'Custom Profile Site',
            'timezone' => 'Europe/London',
            'locale' => 'en_GB',
            'currency' => 'GBP',
            'order_prefix' => 'PROF',
            'auto_cancel_unpaid_minutes' => '240',
            'daily_order_limit' => '250',
            'allow_guest_checkout' => false,
            'allowed_payment_methods' => ['cash', 'g_cash'],
            'default_payment_method' => 'g_cash',
        ];

        File::put($profilePath, json_encode($profileData));

        Artisan::call('app:bootstrap-customer', [
            '--profile' => $profilePath,
        ]);

        expect(Artisan::output())
            ->toContain('Customer bootstrap complete');

        $site = app(SiteSettings::class);
        expect($site->timezone)->toBe('Europe/London')
            ->and($site->locale)->toBe('en_GB')
            ->and($site->currency)->toBe('GBP');

        $order = app(OrderSettings::class);
        expect($order->auto_cancel_unpaid_minutes)->toBe(240)
            ->and($order->allow_guest_checkout)->toBeFalse();

        File::delete($profilePath);
    });

    it('uses default config when neither CLI option nor profile provided', function () {
        $hashPassword = bcrypt('configpass');

        Artisan::call('app:bootstrap-customer', [
            '--owner-name' => 'Config Default Admin',
            '--owner-email' => 'configdefault@example.com',
            '--owner-password-hash' => $hashPassword,
            '--allowed-payment-methods' => 'cash',
            '--default-payment-method' => 'cash',
        ]);

        expect(Artisan::output())
            ->toContain('Customer bootstrap complete');

        $admin = Admin::query()
            ->where('email', 'configdefault@example.com')
            ->first();

        expect($admin)->not->toBeNull();
    });

    it('has correct precedence: CLI > Profile > Config Defaults', function () {
        $profilePath = storage_path('testing/full-precedence-profile.json');
        File::ensureDirectoryExists(dirname($profilePath));

        $hashPassword = bcrypt('profilepass');

        // Profile data differs from CLI and defaults
        $profileData = [
            'owner_name' => 'Profile Name',
            'owner_email' => 'precedence@test.com',
            'owner_password_hash' => $hashPassword,
            'site_name' => 'Profile Site Name',
            'legal_name' => 'Profile Legal',
            'support_email' => 'support@profile.com',
            'timezone' => 'Pacific/Auckland',
            'locale' => 'en_NZ',
            'currency' => 'NZD',
            'order_prefix' => 'PROF',
            'auto_cancel_unpaid_minutes' => '180',
            'allowed_payment_methods' => ['g_cash'],
            'default_payment_method' => 'g_cash',
        ];

        File::put($profilePath, json_encode($profileData));

        // CLI options should win
        Artisan::call('app:bootstrap-customer', [
            '--profile' => $profilePath,
            '--owner-name' => 'CLI Name Wins',
            '--site-name' => 'CLI Site Wins',
            '--support-email' => 'support@cli.com',
            '--timezone' => 'UTC',
            '--order-prefix' => 'CLI',
        ]);

        expect(Artisan::output())
            ->toContain('Customer bootstrap complete');

        $admin = Admin::query()
            ->where('email', 'precedence@test.com')
            ->first();

        expect($admin->name)->toBe('CLI Name Wins');

        $site = app(SiteSettings::class);
        expect($site->name)->toBe('CLI Site Wins')
            ->and($site->support_email)->toBe('support@cli.com')
            ->and($site->timezone)->toBe('UTC')
            ->and($site->legal_name)->toBe('Profile Legal') // From profile (not overridden)
            ->and($site->currency)->toBe('NZD'); // From profile (not overridden)

        $order = app(OrderSettings::class);
        expect($order->prefix)->toBe('CLI')
            ->and($order->auto_cancel_unpaid_minutes)->toBe(180); // From profile

        File::delete($profilePath);
    });
});

describe('Bootstrap Customer Command - Admin and Settings State', function () {
    it('creates or updates admin with correct role', function () {
        Artisan::call('app:bootstrap-customer', [
            '--owner-name' => 'New Super Admin',
            '--owner-email' => 'newsuper@example.com',
            '--owner-password-hash' => bcrypt('superpass'),
            '--timezone' => 'UTC',
            '--locale' => 'en',
            '--currency' => 'USD',
            '--allowed-payment-methods' => 'cash',
            '--default-payment-method' => 'cash',
        ]);

        $admin = Admin::query()
            ->where('email', 'newsuper@example.com')
            ->first();

        expect($admin)
            ->not->toBeNull()
            ->and($admin->hasRole('super_admin'))->toBeTrue();
    });

    it('uses firstOrCreate for existing admin with same email', function () {
        $existingAdmin = Admin::factory()
            ->create(['email' => 'existing@test.com', 'name' => 'Old Name']);

        Artisan::call('app:bootstrap-customer', [
            '--owner-name' => 'Updated Name',
            '--owner-email' => 'existing@test.com',
            '--owner-password-hash' => bcrypt('updatedpass'),
            '--timezone' => 'UTC',
            '--locale' => 'en',
            '--currency' => 'USD',
            '--allowed-payment-methods' => 'cash',
            '--default-payment-method' => 'cash',
        ]);

        $admin = Admin::query()
            ->where('email', 'existing@test.com')
            ->first();

        // firstOrCreate returns existing record without updating
        expect($admin->name)->toBe('Old Name')
            ->and($admin->email)->toBe('existing@test.com');
    });

    it('persists all site settings correctly', function () {
        Artisan::call('app:bootstrap-customer', [
            '--owner-name' => 'Settings Admin',
            '--owner-email' => 'settings@test.com',
            '--owner-password-hash' => bcrypt('settingspass'),
            '--site-name' => 'My Shop',
            '--legal-name' => 'My Shop Inc.',
            '--support-email' => 'support@myshop.com',
            '--support-phone' => '+1-555-0100',
            '--website-url' => 'https://myshop.example.com',
            '--address' => '123 Main St, Anytown, USA',
            '--tax-number' => 'TAX123456',
            '--registration-number' => 'REG789012',
            '--invoice-footer' => 'Thank you for your business!',
            '--timezone' => 'America/Chicago',
            '--locale' => 'en_US',
            '--currency' => 'USD',
            '--allowed-payment-methods' => 'cash',
            '--default-payment-method' => 'cash',
        ]);

        expect(Artisan::output())
            ->toContain('Customer bootstrap complete');

        $site = app(SiteSettings::class);
        expect($site->name)->toBe('My Shop')
            ->and($site->legal_name)->toBe('My Shop Inc.')
            ->and($site->support_email)->toBe('support@myshop.com')
            ->and($site->support_phone)->toBe('+1-555-0100')
            ->and($site->website_url)->toBe('https://myshop.example.com')
            ->and($site->address)->toBe('123 Main St, Anytown, USA')
            ->and($site->tax_number)->toBe('TAX123456')
            ->and($site->registration_number)->toBe('REG789012')
            ->and($site->invoice_footer)->toBe('Thank you for your business!')
            ->and($site->timezone)->toBe('America/Chicago')
            ->and($site->locale)->toBe('en_US')
            ->and($site->currency)->toBe('USD');
    });

    it('persists all order settings correctly', function () {
        Artisan::call('app:bootstrap-customer', [
            '--owner-name' => 'Order Settings Admin',
            '--owner-email' => 'ordersettings@test.com',
            '--owner-password-hash' => bcrypt('orderpass'),
            '--order-prefix' => 'SHOP2024',
            '--auto-cancel-unpaid-minutes' => '180',
            '--daily-order-limit' => '999',
            '--allow-guest-checkout' => '1',
            '--allowed-payment-methods' => 'cash,g_cash',
            '--default-payment-method' => 'g_cash',
            '--timezone' => 'UTC',
            '--locale' => 'en',
            '--currency' => 'USD',
        ]);

        expect(Artisan::output())
            ->toContain('Customer bootstrap complete');

        $order = app(OrderSettings::class);
        expect($order->prefix)->toBe('SHOP2024')
            ->and($order->auto_cancel_unpaid_minutes)->toBe(180)
            ->and($order->daily_order_limit)->toBe(999)
            ->and($order->allow_guest_checkout)->toBeTrue()
            ->and($order->default_payment_method)->toBe('g_cash')
            ->and($order->allowed_payment_methods)->toContain('cash')
            ->and($order->allowed_payment_methods)->toContain('g_cash');
    });

    it('outputs correct summary information', function () {
        Artisan::call('app:bootstrap-customer', [
            '--owner-name' => 'Summary Admin',
            '--owner-email' => 'summary@test.com',
            '--owner-password-hash' => bcrypt('summarypass'),
            '--site-name' => 'Summary Store',
            '--timezone' => 'Europe/Paris',
            '--locale' => 'en',
            '--currency' => 'EUR',
            '--order-prefix' => 'SUM',
            '--default-payment-method' => 'cash',
            '--allowed-payment-methods' => 'cash',
        ]);

        expect(Artisan::output())
            ->toContain('Customer bootstrap complete.')
            ->toContain('summary@test.com')
            ->toContain('Summary Store')
            ->toContain('EUR')
            ->toContain('Europe/Paris')
            ->toContain('prefix=SUM')
            ->toContain('default-payment=cash');
    });
});
