<?php

declare(strict_types=1);

use App\Settings\OrderSettings;
use App\Settings\SiteSettings;
use Spatie\LaravelSettings\Migrations\SettingsBlueprint;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->inGroup(SiteSettings::group(), function (SettingsBlueprint $blueprint): void {
            $blueprint->add('legal_name');
            $blueprint->add('support_email');
            $blueprint->add('support_phone');
            $blueprint->add('timezone', config()->string('app-default.timezone'));
            $blueprint->add('locale', config()->string('app.locale'));
            $blueprint->add('currency', 'PHP');
            $blueprint->add('tax_number');
            $blueprint->add('registration_number');
            $blueprint->add('website_url');
            $blueprint->add('invoice_footer');
        });

        $this->migrator->inGroup(OrderSettings::group(), function (SettingsBlueprint $blueprint): void {
            $blueprint->add('auto_cancel_unpaid_minutes', 0);
            $blueprint->add('allow_guest_checkout', true);
            $blueprint->add('allowed_payment_methods', ['cash', 'g_cash']);
            $blueprint->add('default_payment_method', 'cash');
            $blueprint->add('daily_order_limit', 0);
        });
    }
};
