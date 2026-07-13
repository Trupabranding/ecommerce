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

return Rector\Config\RectorConfig::configure()
    ->withPhpSets()
    ->withPaths([
        __DIR__.'/app',
        __DIR__.'/database',
        __DIR__.'/domain',
        __DIR__.'/resources',
        __DIR__.'/routes',
        __DIR__.'/support',
        __DIR__.'/tests',
    ])
    ->withSets([
        RectorLaravel\Set\LaravelSetList::LARAVEL_120,
        RectorLaravel\Set\LaravelSetList::LARAVEL_ARRAY_STR_FUNCTION_TO_STATIC_CALL,
        //        LaravelSetList::LARAVEL_FACADE_ALIASES_TO_FULL_NAMES,
    ])
    ->withRules([
        Spatie\Ray\Rector\RemoveRayCallRector::class,
        RectorLaravel\Rector\Class_\AddExtendsAnnotationToModelFactoriesRector::class,
        RectorLaravel\Rector\ClassMethod\AddGenericReturnTypeToRelationsRector::class,
        RectorLaravel\Rector\ClassMethod\AddParentBootToModelClassMethodRector::class,
        RectorLaravel\Rector\ClassMethod\AddParentRegisterToEventServiceProviderRector::class,
        //        RectorLaravel\Rector\Class_\AnonymousMigrationsRector::class,
        RectorLaravel\Rector\Expr\AppEnvironmentComparisonToParameterRector::class,
        RectorLaravel\Rector\MethodCall\AssertStatusToAssertMethodRector::class,
        RectorLaravel\Rector\StaticCall\DispatchToHelperFunctionsRector::class,
        RectorLaravel\Rector\MethodCall\EloquentOrderByToLatestOrOldestRector::class,
        RectorLaravel\Rector\PropertyFetch\OptionalToNullsafeOperatorRector::class,
        RectorLaravel\Rector\FuncCall\RemoveDumpDataDeadCodeRector::class,
        RectorLaravel\Rector\Expr\SubStrToStartsWithOrEndsWithStaticMethodCallRector\SubStrToStartsWithOrEndsWithStaticMethodCallRector::class,
        RectorLaravel\Rector\MethodCall\UseComponentPropertyWithinCommandsRector::class,
        RectorLaravel\Rector\MethodCall\EloquentWhereRelationTypeHintingParameterRector::class,
        RectorLaravel\Rector\MethodCall\EloquentWhereTypeHintClosureParameterRector::class,
    ])
    ->withCache(
        cacheDirectory: 'build/rector',
        cacheClass: Rector\Caching\ValueObject\Storage\FileCacheStorage::class,
    );
