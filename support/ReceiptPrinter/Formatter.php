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

namespace Support\ReceiptPrinter;

use Illuminate\Support\Number;
use Illuminate\Support\Str;
use Support\ReceiptPrinter\Data\ItemData;
use Support\ReceiptPrinter\Enums\PaperSizeForItem;

final class Formatter
{
    private function __construct() {}

    public static function header(string $left_text, string $right_text, bool $doubleWidth = false): string
    {
        $width = $doubleWidth ? 8 : 15;

        return Str::padRight($left_text, $width).Str::padLeft($right_text, $width);
    }

    public static function item(ItemData $item, PaperSizeForItem $paperSize = PaperSizeForItem::_57mm): string
    {
        $size = $paperSize->size();

        $name = Str::padRight($item->name, 16);
        $price = Str::padRight(Number::format(moneyAmountToFloat($item->price)).' x '.$item->quantity, $size->padRight);
        /** @var string $subtotal */
        $subtotal = Number::format(moneyAmountToFloat($item->subTotal));
        $subtotal = Str::padLeft($subtotal, $size->padLeft);

        return $name.PHP_EOL.$price.$subtotal.PHP_EOL;
    }

    public static function summary(string $label, float|int $value, bool $doubleWWidth = false): string
    {
        $padRight = $doubleWWidth ? 5 : 11;
        $padLeft = $doubleWWidth ? 9 : 19;

        /** @var string $valueFormatted */
        $valueFormatted = Number::format($value);

        return Str::padRight($label, $padRight).
            Str::padLeft($valueFormatted, $padLeft);
    }
}
