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

namespace App\Listeners;

use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Spatie\LaravelSettings\Events\SavingSettings;

class SettingsActivityLogListener
{
    public function handle(SavingSettings $event): void
    {
        $old = $event->originalValues;

        if ($old === null) {
            return;
        }

        $new = $event->properties;

        $implodeArray = function (mixed $value) {
            if (is_array($value)) {
                sort($value);

                return implode(', ', $value);
            }

            return $value;
        };

        $old = $old->map($implodeArray);
        $new = $new->map($implodeArray);

        $attributeChanges = $old
            ->diff($new)
            ->keys()
            ->toArray();

        if (blank($attributeChanges)) {
            return;
        }

        $activity = activity()
            ->event('settings updated')
            ->withProperties([
                'old' => Arr::only($old->toArray(), $attributeChanges),
                'attributes' => Arr::only($new->toArray(), $attributeChanges),
            ]);

        $causer = Filament::auth()->user();

        if ($causer instanceof Model) {
            $activity->causedBy($causer);
        }

        $activity->log(Str::headline($event->settings::group()).' Settings Updated.');
    }
}
