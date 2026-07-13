<?php

declare(strict_types=1);
//
// /*
// * Copyright (c) 2023 Lloric Mayuga Garcia
// * All rights reserved.
// *
// * 1. Usage Permissions
// *    This software is licensed exclusively to Lloric Mayuga Garcia. The following restrictions apply:
// *    ✅ Allowed:
// *
// *     - Private use within the authorized organization.
// *     - Internal modifications.
// *     🚫 Not Allowed:
// *
// *     - Redistribution, sublicensing, or public sharing.
// *     - Commercial use outside of the authorized organization.
// * 2. Disclaimer of Warranty
// *    This software is provided "as is", without any warranty of any kind, express or implied, including but not limited to:
// *
// *     - Merchantability
// *     - Fitness for a particular purpose
// *     - Non-infringement
// * 3. Liability Limitation
// *    Under no circumstances shall the author(s) or copyright holders be liable for any claims, damages, or other liabilities arising from the use of this software.
// *
// * 4. Legal Enforcement
// *    Unauthorized use, distribution, or modification is strictly prohibited and may result in legal consequences.
// *
// * 📩 For inquiries, contact: lloricode@gmail.com
// * 🌐 Official Website: https://github.com/lloricode
// * 🛒 Purchase Here: https://lloricode.gumroad.com/l/laravel-filament-point-of-sale
// */
//
// declare(strict_types=1);
//
// namespace App\Providers\Macros;
//
// use Closure;
// use Filament\Actions\Action;
// use Filament\Actions\BulkAction;
// use Filament\Actions\Contracts\HasRecord;
// use Filament\Actions\ExportBulkAction;
// use Filament\Facades\Filament;
// use Illuminate\Database\Eloquent\Model;
// use Illuminate\Support\Str;
// use Spatie\Activitylog\ActivityLogger;
// use Spatie\Activitylog\ActivitylogServiceProvider;
//
// /**
// * @mixin MountableAction
// */
// class FilamentMountableActionMixin
// {
//    public function withActivityLog(): Closure
//    {
//        return fn (
//            ?string $logName = null,
//            Closure|string|null $event = null,
//            Closure|string|null $description = null,
//            Closure|array|null $properties = null,
//            Model|int|string|null $causedBy = null
//        ): Action => $this->after(function (Action $action) use ($logName, $event, $description, $properties, $causedBy) {
//
//            $event = $action->evaluate($event) ?? $action->getName();
//            $properties = $action->evaluate($properties);
//            $description = Str::headline(
//                $action->evaluate($description ?? $event) ?? $action->getName()
//            );
//            $causedBy ??= filament_admin();
//
//            $log = function (?Model $model) use ($properties, $event, $logName, $description, $causedBy): void {
//                if ($model !== null && $model::class === ActivitylogServiceProvider::determineActivityModel()) {
//                    return;
//                }
//
//                activity($logName)
//                    ->event($event)
//                    ->causedBy($causedBy)
//                    ->when(
//                        $model,
//                        fn (ActivityLogger $activityLogger, Model $model) => $activityLogger
//                            ->performedOn($model)
//                    )
//                    ->withProperties($properties)
//                    ->log($description);
//            };
//
//            if ($action instanceof BulkAction) {
//
//                if ($action instanceof ExportBulkAction) {
//                    $MODEL = $action->getExporter()::getModel();
//                    $action->getRecords()
//                        ?->each(fn (int|string $modelKey) => $log($MODEL::find($modelKey)));
//                } else {
//                    $action->getRecords()
//                        ?->each(fn (Model $model) => $log($model));
//                }
//
//                return;
//            }
//
//            if ($action instanceof HasRecord) {
//                $log($action->getRecord());
//            }
//        });
//    }
// }
