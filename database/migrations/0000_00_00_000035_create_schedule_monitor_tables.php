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

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScheduleMonitorTables extends Migration
{
    public function up()
    {
        Schema::create('monitored_scheduled_tasks', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name');
            $table->string('type')->nullable();
            $table->string('cron_expression');
            $table->string('timezone')->nullable();
            $table->string('ping_url')->nullable();

            $table->dateTime('last_started_at')->nullable();
            $table->dateTime('last_finished_at')->nullable();
            $table->dateTime('last_failed_at')->nullable();
            $table->dateTime('last_skipped_at')->nullable();

            $table->dateTime('registered_on_oh_dear_at')->nullable();
            $table->dateTime('last_pinged_at')->nullable();
            $table->integer('grace_time_in_minutes');

            $table->timestamps();
        });

        Schema::create('monitored_scheduled_task_log_items', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('monitored_scheduled_task_id');
            $table
                ->foreign('monitored_scheduled_task_id', 'fk_scheduled_task_id')
                ->references('id')
                ->on('monitored_scheduled_tasks')
                ->cascadeOnDelete();

            $table->string('type');

            $table->json('meta')->nullable();

            $table->timestamps();
        });
    }
}
