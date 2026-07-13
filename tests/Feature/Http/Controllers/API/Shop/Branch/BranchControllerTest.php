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

use Domain\Shop\Branch\Models\Branch;

use function Pest\Laravel\assertDatabaseEmpty;
use function Pest\Laravel\getJson;

dataset(
    'includes',
    [
        'media',
    ]
);

it('list', function (?string $include) {

    assertDatabaseEmpty(Branch::class);

    Branch::factory()
        ->hasSpecificMedia()
        ->enabled()
        ->count(3)
        ->sequence(
            [
                'name' => 'Branch 1',
                'address' => 'Address 1',
                'phone' => 'Phone 1',
                'email' => 'Email 1',
                'website' => 'Website 1',
            ],
            [
                'name' => 'Branch 2',
                'address' => 'Address 2',
                'phone' => 'Phone 2',
                'email' => 'Email 2',
                'website' => 'Website 2',
            ],
            [
                'name' => 'Branch 3',
                'address' => 'Address 3',
                'phone' => 'Phone 3',
                'email' => 'Email 3',
                'website' => 'Website 3',
            ],
        )
        ->create();

    $response = getJson('api/branches?include='.$include)
        ->assertOk();

    expect($response)->toMatchSnapshot();
})
    ->with('includes');

it('show', function (?string $include) {

    assertDatabaseEmpty(Branch::class);

    $branch = Branch::factory()
        ->hasSpecificMedia()
        ->enabled()
        ->createOne([
            'name' => 'Branch 1',
            'address' => 'Address 1',
            'phone' => 'Phone 1',
            'email' => 'Email 1',
            'website' => 'Website 1',
        ]);

    $response = getJson('api/branches/'.$branch->getRouteKey().'?include='.$include)
        ->assertOk();

    expect($response)->toMatchSnapshot();
})
    ->with('includes');
