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

namespace Database\Seeders;

use Domain\Settings\Models\SettingCategory;
use Domain\Settings\Models\SettingFeature;
use Illuminate\Database\Seeder;

class SettingFeaturesSeeder extends Seeder
{
    public function run(): void
    {
        // Create categories - 8 eCommerce categories
        $categories = [
            [
                'slug' => 'store',
                'name' => 'Store/Storefront',
                'description' => 'Store configuration, branding, and basic settings',
            ],
            [
                'slug' => 'products',
                'name' => 'Products',
                'description' => 'Product management and catalog settings',
            ],
            [
                'slug' => 'inventory',
                'name' => 'Inventory & Stock',
                'description' => 'Inventory tracking and stock management',
            ],
            [
                'slug' => 'payments',
                'name' => 'Payments',
                'description' => 'Payment methods and transaction settings',
            ],
            [
                'slug' => 'shipping',
                'name' => 'Shipping',
                'description' => 'Shipping methods and delivery options',
            ],
            [
                'slug' => 'notifications',
                'name' => 'Notifications & Communications',
                'description' => 'Email and notification preferences',
            ],
            [
                'slug' => 'seo',
                'name' => 'SEO & Search',
                'description' => 'Search engine optimization and indexing',
            ],
            [
                'slug' => 'integrations',
                'name' => 'Integrations & Advanced',
                'description' => 'Third-party integrations and APIs',
            ],
        ];

        $createdCategories = [];
        foreach ($categories as $category) {
            $createdCategories[$category['slug']] = SettingCategory::firstOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }

        // Define features by category - 45-50 eCommerce features
        $features = [
            'store' => [
                [
                    'key' => 'setting_store_name',
                    'name' => 'Store Name',
                    'description' => 'Your store\'s name displayed to customers',
                    'enabled' => true,
                    'default_value' => 'My Store',
                ],
                [
                    'key' => 'setting_store_logo',
                    'name' => 'Store Logo',
                    'description' => 'Main logo for your store header',
                    'enabled' => true,
                    'default_value' => null,
                ],
                [
                    'key' => 'setting_store_favicon',
                    'name' => 'Store Favicon',
                    'description' => 'Favicon displayed in browser tabs',
                    'enabled' => true,
                    'default_value' => null,
                ],
                [
                    'key' => 'setting_store_address',
                    'name' => 'Store Address',
                    'description' => 'Physical address of your business',
                    'enabled' => true,
                    'default_value' => null,
                ],
                [
                    'key' => 'setting_store_phone',
                    'name' => 'Support Phone',
                    'description' => 'Phone number for customer support',
                    'enabled' => true,
                    'default_value' => null,
                ],
                [
                    'key' => 'setting_store_email',
                    'name' => 'Support Email',
                    'description' => 'Email address for customer inquiries',
                    'enabled' => true,
                    'default_value' => 'support@store.com',
                ],
                [
                    'key' => 'setting_store_timezone',
                    'name' => 'Store Timezone',
                    'description' => 'Timezone for order processing and reporting',
                    'enabled' => true,
                    'default_value' => 'UTC',
                ],
                [
                    'key' => 'setting_store_currency',
                    'name' => 'Default Currency',
                    'description' => 'Primary currency for prices and transactions',
                    'enabled' => true,
                    'default_value' => 'USD',
                ],
            ],
            'products' => [
                [
                    'key' => 'setting_product_default_status',
                    'name' => 'Default Product Status',
                    'description' => 'Status assigned to newly created products',
                    'enabled' => true,
                    'default_value' => 'draft',
                ],
                [
                    'key' => 'setting_product_enable_reviews',
                    'name' => 'Enable Product Reviews',
                    'description' => 'Allow customers to leave product reviews',
                    'enabled' => true,
                    'default_value' => true,
                ],
                [
                    'key' => 'setting_product_require_sku',
                    'name' => 'Require Product SKU',
                    'description' => 'SKU field must be filled for all products',
                    'enabled' => true,
                    'default_value' => false,
                ],
                [
                    'key' => 'setting_product_visibility_default',
                    'name' => 'Default Product Visibility',
                    'description' => 'Default visibility setting for new products',
                    'enabled' => true,
                    'default_value' => 'public',
                ],
                [
                    'key' => 'setting_product_auto_publish',
                    'name' => 'Auto-Publish Products',
                    'description' => 'Automatically publish products when created',
                    'enabled' => false,
                    'default_value' => false,
                ],
                [
                    'key' => 'setting_product_image_limit',
                    'name' => 'Product Image Upload Limit',
                    'description' => 'Maximum number of images per product',
                    'enabled' => true,
                    'default_value' => 10,
                ],
            ],
            'inventory' => [
                [
                    'key' => 'setting_inventory_low_stock_threshold',
                    'name' => 'Low Stock Warning Threshold',
                    'description' => 'Quantity level that triggers low stock warning',
                    'enabled' => true,
                    'default_value' => 10,
                ],
                [
                    'key' => 'setting_inventory_critical_level',
                    'name' => 'Critical Stock Level',
                    'description' => 'Critical quantity level for orders',
                    'enabled' => true,
                    'default_value' => 5,
                ],
                [
                    'key' => 'setting_inventory_display_format',
                    'name' => 'Stock Display Format',
                    'description' => 'How stock quantity is displayed to customers',
                    'enabled' => true,
                    'default_value' => 'numeric',
                ],
                [
                    'key' => 'setting_inventory_allow_backorders',
                    'name' => 'Allow Backorders',
                    'description' => 'Permit orders for out-of-stock items',
                    'enabled' => true,
                    'default_value' => false,
                ],
                [
                    'key' => 'setting_inventory_track_by_location',
                    'name' => 'Track Stock by Location',
                    'description' => 'Enable location-based inventory tracking',
                    'enabled' => false,
                    'default_value' => false,
                ],
            ],
            'payments' => [
                [
                    'key' => 'setting_payment_enabled_methods',
                    'name' => 'Enabled Payment Methods',
                    'description' => 'Payment methods available to customers',
                    'enabled' => true,
                    'default_value' => ['credit_card', 'paypal'],
                ],
                [
                    'key' => 'setting_payment_default_method',
                    'name' => 'Default Payment Method',
                    'description' => 'Default payment method for checkout',
                    'enabled' => true,
                    'default_value' => 'credit_card',
                ],
                [
                    'key' => 'setting_payment_timeout',
                    'name' => 'Payment Timeout',
                    'description' => 'Seconds before payment processing times out',
                    'enabled' => true,
                    'default_value' => 30,
                ],
                [
                    'key' => 'setting_payment_allow_partial',
                    'name' => 'Allow Partial Payments',
                    'description' => 'Permit customers to make partial payments',
                    'enabled' => false,
                    'default_value' => false,
                ],
                [
                    'key' => 'setting_payment_pci_level',
                    'name' => 'PCI Compliance Level',
                    'description' => 'PCI DSS compliance level for payment processing',
                    'enabled' => true,
                    'default_value' => '3',
                ],
                [
                    'key' => 'setting_payment_auto_invoice',
                    'name' => 'Auto-Generate Invoices',
                    'description' => 'Automatically create invoices for orders',
                    'enabled' => true,
                    'default_value' => true,
                ],
            ],
            'shipping' => [
                [
                    'key' => 'setting_shipping_providers',
                    'name' => 'Shipping Providers',
                    'description' => 'Enabled shipping carriers and providers',
                    'enabled' => true,
                    'default_value' => ['usps', 'fedex', 'ups'],
                ],
                [
                    'key' => 'setting_shipping_default_method',
                    'name' => 'Default Shipping Method',
                    'description' => 'Default shipping option in checkout',
                    'enabled' => true,
                    'default_value' => 'standard',
                ],
                [
                    'key' => 'setting_shipping_free_threshold',
                    'name' => 'Free Shipping Threshold',
                    'description' => 'Order amount that qualifies for free shipping',
                    'enabled' => true,
                    'default_value' => 100,
                ],
                [
                    'key' => 'setting_shipping_pickup_available',
                    'name' => 'Pickup Available',
                    'description' => 'Enable customer pickup option',
                    'enabled' => false,
                    'default_value' => false,
                ],
                [
                    'key' => 'setting_shipping_dimensional_weight',
                    'name' => 'Dimensional Weight Pricing',
                    'description' => 'Use dimensional weight for rate calculation',
                    'enabled' => false,
                    'default_value' => false,
                ],
                [
                    'key' => 'setting_shipping_max_distance',
                    'name' => 'Max Shipping Distance',
                    'description' => 'Maximum distance in miles for shipping',
                    'enabled' => false,
                    'default_value' => 5000,
                ],
            ],
            'notifications' => [
                [
                    'key' => 'setting_notification_from_email',
                    'name' => 'Email From Address',
                    'description' => 'Email address used for sending notifications',
                    'enabled' => true,
                    'default_value' => 'noreply@store.com',
                ],
                [
                    'key' => 'setting_notification_from_name',
                    'name' => 'Email From Name',
                    'description' => 'Display name for notification emails',
                    'enabled' => true,
                    'default_value' => 'My Store',
                ],
                [
                    'key' => 'setting_notification_admin_emails',
                    'name' => 'Admin Notification Recipients',
                    'description' => 'Email addresses that receive admin notifications',
                    'enabled' => true,
                    'default_value' => ['admin@store.com'],
                ],
                [
                    'key' => 'setting_notification_send_order_emails',
                    'name' => 'Send Order Emails',
                    'description' => 'Send email confirmations for orders',
                    'enabled' => true,
                    'default_value' => true,
                ],
                [
                    'key' => 'setting_notification_inventory_alerts',
                    'name' => 'Send Inventory Alerts',
                    'description' => 'Alert when stock falls below threshold',
                    'enabled' => true,
                    'default_value' => true,
                ],
                [
                    'key' => 'setting_notification_queue_enabled',
                    'name' => 'Email Queue',
                    'description' => 'Queue emails for batch processing',
                    'enabled' => true,
                    'default_value' => true,
                ],
            ],
            'seo' => [
                [
                    'key' => 'setting_seo_meta_title_template',
                    'name' => 'Meta Title Template',
                    'description' => 'Template for page meta titles',
                    'enabled' => true,
                    'default_value' => '{title} - My Store',
                ],
                [
                    'key' => 'setting_seo_meta_description_template',
                    'name' => 'Meta Description Template',
                    'description' => 'Template for page meta descriptions',
                    'enabled' => true,
                    'default_value' => '{description}',
                ],
                [
                    'key' => 'setting_seo_url_slug_format',
                    'name' => 'URL Slug Format',
                    'description' => 'Format for product and category URLs',
                    'enabled' => true,
                    'default_value' => 'name-id',
                ],
                [
                    'key' => 'setting_seo_sitemap_enabled',
                    'name' => 'Generate Sitemap',
                    'description' => 'Automatically generate XML sitemap',
                    'enabled' => true,
                    'default_value' => true,
                ],
                [
                    'key' => 'setting_seo_canonical_tags',
                    'name' => 'Canonical Tags',
                    'description' => 'Add canonical tags to prevent duplicate content',
                    'enabled' => true,
                    'default_value' => true,
                ],
                [
                    'key' => 'setting_seo_google_analytics_id',
                    'name' => 'Google Analytics ID',
                    'description' => 'Google Analytics tracking ID',
                    'enabled' => true,
                    'default_value' => null,
                ],
            ],
            'integrations' => [
                [
                    'key' => 'setting_integration_stripe_api_key',
                    'name' => 'Stripe API Key',
                    'description' => 'Secret API key for Stripe payments',
                    'enabled' => true,
                    'default_value' => null,
                ],
                [
                    'key' => 'setting_integration_mailchimp_api_key',
                    'name' => 'Mailchimp API Key',
                    'description' => 'API key for Mailchimp email marketing',
                    'enabled' => false,
                    'default_value' => null,
                ],
                [
                    'key' => 'setting_integration_sendgrid_api_key',
                    'name' => 'SendGrid API Key',
                    'description' => 'API key for SendGrid email service',
                    'enabled' => false,
                    'default_value' => null,
                ],
                [
                    'key' => 'setting_integration_slack_webhook',
                    'name' => 'Slack Webhook',
                    'description' => 'Enable Slack notifications for events',
                    'enabled' => false,
                    'default_value' => false,
                ],
                [
                    'key' => 'setting_integration_api_rate_limit',
                    'name' => 'API Rate Limit',
                    'description' => 'Requests per minute for API endpoints',
                    'enabled' => true,
                    'default_value' => 60,
                ],
            ],
        ];

        // Create features
        foreach ($features as $categorySlug => $categoryFeatures) {
            $category = $createdCategories[$categorySlug];

            foreach ($categoryFeatures as $feature) {
                SettingFeature::firstOrCreate(
                    ['key' => $feature['key']],
                    [
                        'setting_category_uuid' => $category->uuid,
                        ...$feature,
                    ]
                );
            }
        }
    }
}
