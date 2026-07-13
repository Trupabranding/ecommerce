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

namespace Support\ReceiptPrinter\Data;

use Mike42\Escpos\Printer;
use Spatie\Cloneable\Cloneable;

// https://github.com/charlieuki/receipt-printer
readonly class ReceiptPrinterData
{
    use Cloneable;

    public StoreData $store;

    /**
     * @param  array<int, ItemData>  $items
     */
    public function __construct(
        public array $items = [],
        public float $taxPercentage = 0,
        public string $transactionId = '',
        public ?string $logo = null,
        public array $qrCode = [],
    ) {
        $this->store = new StoreData('', '', '', '', '', '');
    }

    public function store(StoreData $store): self
    {
        return $this->with(store: $store);
    }

    /**
     * @param  array<int, ItemData>  $items
     */
    public function items(array $items): self
    {
        return $this->with(items: $items);
    }

    public function logo(string $logo): self
    {
        return $this->with(logo: $logo);
    }

    public function transactionId(string $transactionId): self
    {
        return $this->with(transactionId: $transactionId);
    }

    public function taxPercentage(float $taxPercentage): self
    {
        return $this->with(taxPercentage: $taxPercentage);
    }

    public function qrCode(array $qrCode): self
    {
        return $this->with(qrCode: $qrCode);
    }
}
