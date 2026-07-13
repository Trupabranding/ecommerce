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

arch('Not debugging statements are left in our code.')
    ->expect(['dd', 'dump', 'ray', 'rd', 'die', 'eval', 'sleep', 'debug', 'var_dump', 'env'])
    ->not->toBeUsed();

// arch()
//    ->preset()
//    ->laravel();
// //    ->ignoring([]);
arch()
    ->preset()
    ->php()
    ->ignoring(['debug_backtrace']);
arch()
    ->preset()
    ->security()
    ->ignoring(['sha1']);
// arch()->preset()->strict();
// arch()->preset()->relaxed();

function domainEnums(): array
{
    return [
        'Domain\Access\Admin\Enums',
        'Domain\Access\Role\Enums',
        'Domain\Shop\Branch\Enums',
        'Domain\Shop\Brand\Enums',
        'Domain\Shop\Category\Enums',
        'Domain\Shop\Customer\Enums',
        'Domain\Shop\Order\Enums',
        'Domain\Shop\Product\Enums',
        'Domain\Shop\Stock\Enums',
    ];
};
function domainActions(): array
{
    return [
        'Domain\Access\Admin\Actions',
        'Domain\Access\Role\Actions',
        'Domain\Shop\Branch\Actions',
        'Domain\Shop\Brand\Actions',
        'Domain\Shop\Category\Actions',
        'Domain\Shop\Customer\Actions',
        'Domain\Shop\Order\Actions',
        'Domain\Shop\Product\Actions',
        'Domain\Shop\Stock\Actions',
    ];
};
function domainDTOs(): array
{
    return [
        'Domain\Access\Admin\DataTransferObjects',
        'Domain\Access\Role\DataTransferObjects',
        'Domain\Shop\Branch\DataTransferObjects',
        'Domain\Shop\Brand\DataTransferObjects',
        'Domain\Shop\Category\DataTransferObjects',
        'Domain\Shop\Customer\DataTransferObjects',
        'Domain\Shop\Order\DataTransferObjects',
        'Domain\Shop\Product\DataTransferObjects',
        'Domain\Shop\Stock\DataTransferObjects',
    ];
};
function domainModels(): array
{
    return [
        'Domain\Access\Admin\Models',
        'Domain\Access\Role\Models',
        'Domain\Shop\Branch\Models',
        'Domain\Shop\Brand\Models',
        'Domain\Shop\Category\Models',
        'Domain\Shop\Customer\Models',
        'Domain\Shop\Order\Models',
        'Domain\Shop\Product\Models',
        'Domain\Shop\Stock\Models',
    ];
};
function domainModelEloquentBuilder(): array
{
    return [
        'Domain\Access\Admin\Models\EloquentBuilder',
        'Domain\Access\Role\Models\EloquentBuilder',
        'Domain\Shop\Branch\Models\EloquentBuilder',
        'Domain\Shop\Brand\Models\EloquentBuilder',
        'Domain\Shop\Category\Models\EloquentBuilder',
        'Domain\Shop\Customer\Models\EloquentBuilder',
        'Domain\Shop\Order\Models\EloquentBuilder',
        'Domain\Shop\Product\Models\EloquentBuilder',
        'Domain\Shop\Stock\Models\EloquentBuilder',
    ];
};
function domainObservers(): array
{
    return [
        'Domain\Access\Admin\Observers',
        'Domain\Access\Role\Observers',
        'Domain\Shop\Branch\Observers',
        'Domain\Shop\Brand\Observers',
        'Domain\Shop\Category\Observers',
        'Domain\Shop\Customer\Observers',
        'Domain\Shop\Order\Observers',
        'Domain\Shop\Product\Observers',
        'Domain\Shop\Stock\Observers',
    ];
};
function domainPolicies(): array
{
    return [
        'Domain\Access\Admin\Policies',
        'Domain\Access\Role\Policies',
        'Domain\Shop\Branch\Policies',
        'Domain\Shop\Brand\Policies',
        'Domain\Shop\Category\Policies',
        'Domain\Shop\Customer\Policies',
        'Domain\Shop\Order\Policies',
        'Domain\Shop\Product\Policies',
        'Domain\Shop\Stock\Policies',
    ];
};
function domainFactories(): array
{
    return [
        'Domain\Access\Admin\Database\Factories',
        'Domain\Access\Role\Database\Factories',
        'Domain\Shop\Branch\Database\Factories',
        'Domain\Shop\Brand\Database\Factories',
        'Domain\Shop\Category\Database\Factories',
        'Domain\Shop\Customer\Database\Factories',
        'Domain\Shop\Order\Database\Factories',
        'Domain\Shop\Product\Database\Factories',
        'Domain\Shop\Stock\Database\Factories',
    ];
};
function domainRules(): array
{
    return [
        'Domain\Access\Admin\Rules',
        'Domain\Access\Role\Rules',
        'Domain\Shop\Branch\Rules',
        'Domain\Shop\Brand\Rules',
        'Domain\Shop\Category\Rules',
        'Domain\Shop\Customer\Rules',
        'Domain\Shop\Order\Rules',
        'Domain\Shop\Product\Rules',
        'Domain\Shop\Stock\Rules',
    ];
};

arch('domain enums')
    ->with(fn () => domainEnums())
    ->expect(fn (string $folder) => $folder)
    ->toBeEnums();

arch('domain actions')
    ->with(fn () => domainActions())
    ->expect(fn (string $folder) => $folder)
    // ->toUseNothing() // ErrorException: Attempt to read property "stmts" on null ...
    ->toImplementNothing()
    ->toExtendNothing()
//    ->toBeFinal() // this should be able to mock
    ->toBeReadonly()
    ->toHaveSuffix('Action')
    ->not->toUse('app');

arch('domain DTOs')
    ->with(fn () => domainDTOs())
    ->expect(fn (string $folder) => $folder)
    // ->toUseNothing() // ErrorException: Attempt to read property "stmts" on null ...
    ->toImplementNothing()
    ->toExtendNothing()
    ->toBeFinal()
    ->toBeReadonly()
    ->toHaveSuffix('Data');

arch('domain models')
    ->with(fn () => domainModels())
    ->expect(fn (string $folder) => $folder)
    ->toExtend(Illuminate\Database\Eloquent\Model::class)
    ->ignoring(domainModelEloquentBuilder());

arch('domain model queries')
    ->with(fn () => domainModelEloquentBuilder())
    ->expect(fn (string $folder) => $folder)
    ->toExtend(Illuminate\Database\Eloquent\Builder::class);

arch('domain model factories')
    ->with(fn () => domainFactories())
    ->expect(fn (string $folder) => $folder)
    ->toHaveSuffix('Factory')
    ->toExtend(Illuminate\Database\Eloquent\Factories\Factory::class);

arch('model rules')
    ->with(fn () => domainRules())
    ->expect(fn (string $folder) => $folder)
    ->toHaveSuffix('Rule')
    ->toImplement(Illuminate\Contracts\Validation\ValidationRule::class);

arch('controllers')
    ->expect('App\Http\Controllers')
    ->toExtendNothing() // laravel 11 will delete abstract controller class
    ->classes()
    ->toHaveSuffix('Controller');

arch('model resources')
    ->expect('App\Http\Resources')
    ->toHaveSuffix('Resource')
    ->toExtend(TiMacDonald\JsonApi\JsonApiResource::class);

arch('do not use Illuminate\Http in domain')
    ->expect('Illuminate\Http')
    ->not->toBeUsedIn('Domain');

arch('settings')
    ->expect('App\Settings')
    ->toHaveSuffix('Settings')
    ->toExtend(Spatie\LaravelSettings\Settings::class);

arch('listeners')
    ->expect('App\Listeners')
    ->toHaveSuffix('Listener')
    ->toExtendNothing();

arch('policies')
    ->with(fn () => array_merge(domainPolicies(), ['App\Policies']))
    ->expect(fn (string $folder) => $folder)
    ->toHaveSuffix('Policy')
    ->toImplementNothing()
    ->toExtendNothing();

arch('domain observers')
    ->with(fn () => domainObservers())
    ->expect(fn (string $folder) => $folder)
    ->toHaveSuffix('Observer')
    ->toImplementNothing()
    ->toExtendNothing();

// arch('domain models only use Illuminate\Database')
//    ->expect(fn () => domainModels())
//    ->toOnlyUse('Illuminate\Database')->only();

// arch('domain models usage')
//    ->expect(fn () => domainModels())
//    ->toOnlyBeUsedIn(array_merge(
//        domainActions(),
//        domainObservers(),
//        domainPolicies(),
//        domainFactories(),
//        domainModels(),
//        domainRules(),
//        domainDTOs(),
//        [
//            // Expecting 'Domain\Shop\Order\Models\Order' not to be used on 'Domain\Shop\Order\Exports\ExportOrderReceipt'.
//            //at domain/Shop/Order/Exports/ExportOrderReceipt.php:7
//            'Domain\Shop\Order\Exports',
//            'Database\Seeders',
//            'App\Listeners',
//            'App\Helpers',
//            'App\Http\Resources',
//        ],
//    ));

// arch('domain queue')
//    ->expect('App\Jobs')
//    ->toImplement('Illuminate\Contracts\Queue\ShouldQueue');
