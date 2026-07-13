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
use Domain\Shop\Branch\Models\Branch;
use Domain\Shop\Order\Actions\GenerateReceiptNumberAction;
use Domain\Shop\Order\Models\Order;

use function Pest\Laravel\travelTo;

beforeEach(function () {
    $this->branch = Branch::factory()->createOne(['code' => 'BRANCH_']);

    $orderSettingPrefix = 'SETTING_';
    OrderSettings::fake([
        'prefix' => $orderSettingPrefix,
    ]);

    $this->prefix = $orderSettingPrefix.$this->branch->code;
    $this->prefixWithDate = $this->prefix.'210131';

    $this->generator = app(GenerateReceiptNumberAction::class);

    travelTo(now()->parse('2021-01-31'));
});

it('generate first time', function () {

    expect($this->generator->execute($this->branch))
        ->toBe($this->prefixWithDate.'0001');
});

it('generate first time in the next day', function () {

    travelTo(now()->subDay());

    Order::factory()
        ->for(Branch::factory()->enabled()->createOne())
        ->sequence(
            ['receipt_number' => $this->prefix.'210130'.'0001', 'created_at' => now()->subDays(4)]
        )
        ->createOne();

    travelTo(now()->addDay());

    expect($this->generator->execute($this->branch))
        ->toBe($this->prefixWithDate.'0001');
});

it('generate 3rd time on same day', function () {

    Order::factory()
        ->for($this->branch)
        ->sequence(
            ['receipt_number' => $this->prefixWithDate.'0001', 'created_at' => now()->subSeconds(2)],
            ['receipt_number' => $this->prefixWithDate.'0002', 'created_at' => now()->subSecond()]
        )
        ->count(2)
        ->create();

    expect($this->generator->execute($this->branch))
        ->toBe($this->prefixWithDate.'0003');
});

it('generate 3rd time in the next day', function () {

    travelTo(now()->subDay());

    Order::factory()
        ->for($this->branch)
        ->sequence(
            ['receipt_number' => $this->prefix.'210130'.'0001', 'created_at' => now()->subDays(4)],
            ['receipt_number' => $this->prefix.'210130'.'0002', 'created_at' => now()->subDays(2)]
        )
        ->count(2)
        ->create();

    travelTo(now()->addDay());

    Order::factory()
        ->for($this->branch)
        ->sequence(
            ['receipt_number' => $this->prefixWithDate.'0001', 'created_at' => now()->subDays(4)],
            ['receipt_number' => $this->prefixWithDate.'0002', 'created_at' => now()->subDays(2)]
        )
        ->count(2)
        ->create();

    expect($this->generator->execute($this->branch))
        ->toBe($this->prefixWithDate.'0003');
});
