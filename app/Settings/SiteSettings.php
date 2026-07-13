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

namespace App\Settings;

use Override;

class SiteSettings extends BaseSettings
{
    public string $name;

    public string $favicon;

    public string $logo;

    public ?string $legal_name = null;

    public ?string $support_email = null;

    public ?string $support_phone = null;

    public string $timezone;

    public string $locale;

    public string $currency;

    public ?string $tax_number = null;

    public ?string $registration_number = null;

    public ?string $website_url = null;

    public ?string $address = null;

    public ?string $invoice_footer = null;

    #[Override]
    public static function group(): string
    {
        return 'site';
    }

    public function getSiteFaviconUrl(): string
    {
        return $this->getUrlFromStorage($this->favicon);
    }

    public function getSiteLogoUrl(): string
    {
        return $this->getUrlFromStorage($this->logo);
    }
}
