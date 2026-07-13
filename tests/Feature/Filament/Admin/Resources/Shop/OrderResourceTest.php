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

use App\Filament\Admin\Resources\Shop\OrderResource;
use Domain\Shop\Branch\Models\Branch;
use Domain\Shop\Order\Models\Order;

use function Pest\Laravel\get;

beforeEach(function () {
    loginAsAdmin();
    fakeGenerateReceiptNumberActionFake();
});

it('can delete', function () {
    $order = Order::factory()
        ->for(Branch::factory())
        ->create();

    get(OrderResource::getUrl('view', ['record' => $order]))
        ->assertOk();

    $order->delete();
    expect($order->fresh()->trashed())->toBeTrue();
});

it('can force delete', function () {
    $order = Order::factory()
        ->for(Branch::factory())
        ->create();
    $order->delete();

    expect(Order::withTrashed()->where('uuid', $order->uuid)->exists())->toBeTrue();

    $order->forceDelete();

    expect(Order::withTrashed()->where('uuid', $order->uuid)->exists())->toBeFalse();
});

it('can restore', function () {
    $order = Order::factory()
        ->for(Branch::factory())
        ->create();
    $order->delete();

    expect(Order::withTrashed()->where('uuid', $order->uuid)->first()->deleted_at)->not->toBeNull();

    $order->restore();

    expect(Order::withTrashed()->where('uuid', $order->uuid)->first()->deleted_at)->toBeNull();
});
