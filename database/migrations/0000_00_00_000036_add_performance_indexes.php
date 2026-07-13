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

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Products table indexes
        Schema::table('products', function (Blueprint $table) {
            $table->index('status', 'products_status_idx');
            $table->index('created_at', 'products_created_at_idx');
            $table->index(['category_id', 'status'], 'products_category_status_idx');
            $table->index('brand_id', 'products_brand_id_idx');
        });

        // SKUs table indexes
        Schema::table('skus', function (Blueprint $table) {
            $table->index('product_id', 'skus_product_id_idx');
            $table->index('created_at', 'skus_created_at_idx');
        });

        // Orders table indexes
        Schema::table('orders', function (Blueprint $table) {
            $table->index('status', 'orders_status_idx');
            $table->index('payment_status', 'orders_payment_status_idx');
            $table->index('branch_id', 'orders_branch_id_idx');
            $table->index('customer_id', 'orders_customer_id_idx');
            $table->index('created_at', 'orders_created_at_idx');
            $table->index('purchased_at', 'orders_purchased_at_idx');
            // Composite indexes for common filter combinations
            $table->index(['branch_id', 'status'], 'orders_branch_status_idx');
            $table->index(['status', 'created_at'], 'orders_status_created_at_idx');
        });

        // Order items table indexes
        Schema::table('order_items', function (Blueprint $table) {
            $table->index('order_id', 'order_items_order_id_idx');
            $table->index('sku_id', 'order_items_sku_id_idx');
            $table->index('created_at', 'order_items_created_at_idx');
        });

        // Categories table indexes
        Schema::table('categories', function (Blueprint $table) {
            $table->index('created_at', 'categories_created_at_idx');
            $table->index('parent_uuid', 'categories_parent_uuid_idx');
            $table->index('is_visible', 'categories_is_visible_idx');
        });

        // Branches table indexes
        Schema::table('branches', function (Blueprint $table) {
            $table->index('status', 'branches_status_idx');
            $table->index('created_at', 'branches_created_at_idx');
        });

        // Brands table indexes
        Schema::table('brands', function (Blueprint $table) {
            $table->index('created_at', 'brands_created_at_idx');
        });

        // Customers table indexes
        Schema::table('customers', function (Blueprint $table) {
            $table->index('status', 'customers_status_idx');
            $table->index('email', 'customers_email_idx');
            $table->index('created_at', 'customers_created_at_idx');
        });

        // Stock levels table indexes
        if (Schema::hasTable('sku_stocks')) {
            Schema::table('sku_stocks', function (Blueprint $table) {
                $table->index('sku_id', 'sku_stocks_sku_id_idx');
                $table->index('branch_id', 'sku_stocks_branch_id_idx');
                $table->index(['sku_id', 'branch_id'], 'sku_stocks_sku_branch_idx');
            });
        }

        // Activity log indexes for audit trail performance
        Schema::table('activity_log', function (Blueprint $table) {
            $table->index('subject_type', 'activity_log_subject_type_idx');
            $table->index('subject_id', 'activity_log_subject_id_idx');
            $table->index('causer_id', 'activity_log_causer_id_idx');
            $table->index('created_at', 'activity_log_created_at_idx');
            $table->index(['subject_type', 'subject_id'], 'activity_log_subject_idx');
        });
    }

    public function down(): void
    {
        // Products table
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('products_status_idx');
            $table->dropIndex('products_created_at_idx');
            $table->dropIndex('products_category_status_idx');
            $table->dropIndex('products_brand_id_idx');
        });

        // SKUs table
        Schema::table('skus', function (Blueprint $table) {
            $table->dropIndex('skus_product_id_idx');
            $table->dropIndex('skus_created_at_idx');
        });

        // Orders table
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('orders_status_idx');
            $table->dropIndex('orders_payment_status_idx');
            $table->dropIndex('orders_branch_id_idx');
            $table->dropIndex('orders_customer_id_idx');
            $table->dropIndex('orders_created_at_idx');
            $table->dropIndex('orders_purchased_at_idx');
            $table->dropIndex('orders_branch_status_idx');
            $table->dropIndex('orders_status_created_at_idx');
        });

        // Order items table
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropIndex('order_items_order_id_idx');
            $table->dropIndex('order_items_sku_id_idx');
            $table->dropIndex('order_items_created_at_idx');
        });

        // Categories table
        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex('categories_created_at_idx');
            $table->dropIndex('categories_parent_uuid_idx');
            $table->dropIndex('categories_is_visible_idx');
        });

        // Branches table
        Schema::table('branches', function (Blueprint $table) {
            $table->dropIndex('branches_status_idx');
            $table->dropIndex('branches_created_at_idx');
        });

        // Brands table
        Schema::table('brands', function (Blueprint $table) {
            $table->dropIndex('brands_created_at_idx');
        });

        // Customers table
        Schema::table('customers', function (Blueprint $table) {
            $table->dropIndex('customers_status_idx');
            $table->dropIndex('customers_email_idx');
            $table->dropIndex('customers_created_at_idx');
        });

        // Stock levels table
        if (Schema::hasTable('sku_stocks')) {
            Schema::table('sku_stocks', function (Blueprint $table) {
                $table->dropIndex('sku_stocks_sku_id_idx');
                $table->dropIndex('sku_stocks_branch_id_idx');
                $table->dropIndex('sku_stocks_sku_branch_idx');
            });
        }

        // Activity log table
        Schema::table('activity_log', function (Blueprint $table) {
            $table->dropIndex('activity_log_subject_type_idx');
            $table->dropIndex('activity_log_subject_id_idx');
            $table->dropIndex('activity_log_causer_id_idx');
            $table->dropIndex('activity_log_created_at_idx');
            $table->dropIndex('activity_log_subject_idx');
        });
    }
};
