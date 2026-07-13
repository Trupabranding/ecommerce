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

namespace Domain\Settings\Enums;

enum FeatureCategory: string
{
    case Store = 'store';
    case Products = 'products';
    case Inventory = 'inventory';
    case Payments = 'payments';
    case Shipping = 'shipping';
    case Notifications = 'notifications';
    case SEO = 'seo';
    case Integrations = 'integrations';

    public function label(): string
    {
        return match ($this) {
            self::Store => 'Store/Storefront',
            self::Products => 'Products',
            self::Inventory => 'Inventory & Stock',
            self::Payments => 'Payments',
            self::Shipping => 'Shipping',
            self::Notifications => 'Notifications & Communications',
            self::SEO => 'SEO & Search',
            self::Integrations => 'Integrations & Advanced',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Store => 'Store configuration, branding, and basic settings',
            self::Products => 'Product management and catalog settings',
            self::Inventory => 'Inventory tracking and stock management',
            self::Payments => 'Payment methods and transaction settings',
            self::Shipping => 'Shipping methods and delivery options',
            self::Notifications => 'Email and notification preferences',
            self::SEO => 'Search engine optimization and indexing',
            self::Integrations => 'Third-party integrations and APIs',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Store => 'heroicon-o-building-storefront',
            self::Products => 'heroicon-o-shopping-bag',
            self::Inventory => 'heroicon-o-archive-box',
            self::Payments => 'heroicon-o-credit-card',
            self::Shipping => 'heroicon-o-truck',
            self::Notifications => 'heroicon-o-bell',
            self::SEO => 'heroicon-o-magnifying-glass',
            self::Integrations => 'heroicon-o-puzzle-piece',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Store => 'primary',
            self::Products => 'success',
            self::Inventory => 'info',
            self::Payments => 'warning',
            self::Shipping => 'danger',
            self::Notifications => 'secondary',
            self::SEO => 'purple',
            self::Integrations => 'slate',
        };
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return array_combine(
            array_map(fn ($case) => $case->value, self::cases()),
            array_map(fn ($case) => $case->label(), self::cases())
        );
    }
}
