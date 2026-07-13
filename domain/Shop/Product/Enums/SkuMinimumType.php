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

namespace Domain\Shop\Product\Enums;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Str;

enum SkuMinimumType: string implements HasColor, HasIcon, HasLabel
{
    case minimum_quantity = 'minimum_quantity';
    case minimum_quantity_to_pay = 'minimum_quantity_to_pay';

    public function getColor(): array
    {
        return match ($this) {
            self::minimum_quantity_to_pay => Color::Green,
            self::minimum_quantity => Color::Orange,
        };
    }

    public function getIcon(): \BackedEnum
    {
        return match ($this) {
            self::minimum_quantity_to_pay => Heroicon::OutlinedCheckCircle,
            self::minimum_quantity => Heroicon::OutlinedClock,
        };
    }

    public function getLabel(): string
    {
        return Str::headline($this->value);
    }
}
