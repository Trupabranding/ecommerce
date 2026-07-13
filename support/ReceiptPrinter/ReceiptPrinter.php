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

use Brick\Math\RoundingMode;
use Domain\Access\Admin\Models\Admin;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\CupsPrintConnector;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use Support\ReceiptPrinter\Data\ReceiptPrinterData;

// https://github.com/charlieuki/receipt-printer
final readonly class ReceiptPrinter
{
    private Printer $printer;

    /**
     * @throws \Exception
     */
    public function __construct(private ReceiptPrinterData $receiptPrinterData)
    {
        $connectorDescriptor = config()->string('support.receipt-printer.connector_descriptor');

        $connector = match (config()->string('support.receipt-printer.connector_type')) {
            'cups' => new CupsPrintConnector($connectorDescriptor),
            'windows' => new WindowsPrintConnector($connectorDescriptor),
            'network' => new NetworkPrintConnector($connectorDescriptor),
            'file' => new FilePrintConnector($connectorDescriptor),
            default => throw new \Exception('Invalid printer connector type.'),
        };

        $this->printer = new Printer($connector);

    }

    private function printImage(): void
    {
        if ($this->receiptPrinterData->logo === null) {
            return;
        }

        $image = EscposImage::load($this->receiptPrinterData->logo, false);

        $this->printer->feed();

        //        switch ($mode) {
        //            case 0:
        $this->printer->graphics($image);
        //                break;
        //            case 1:
        //                $this->printer->bitImage($image);
        //                break;
        //            case 2:
        //                $this->printer->bitImageColumnFormat($image);
        //                break;
        //        }

        $this->printer->feed();

    }

    public function send(): void
    {
        $subtotal = money(0);
        foreach ($this->receiptPrinterData->items as $item) {
            $subtotal = $item->subTotal->plus($subtotal);
        }
        $tax = $subtotal->multipliedBy($this->receiptPrinterData->taxPercentage / 100, RoundingMode::Down);
        $grandTotal = $subtotal->plus($tax);

        // Init printer settings
        $this->printer->initialize();
        $this->printer->selectPrintMode();
        // Set margins
        $this->printer->setPrintLeftMargin(1);
        // Print receipt headers
        $this->printer->setJustification(Printer::JUSTIFY_CENTER);

        $this->printImage();

        $this->printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
        $this->printer->feed(2);
        $this->printer->text($this->receiptPrinterData->store->name.PHP_EOL);
        $this->printer->selectPrintMode();
        $this->printer->text($this->receiptPrinterData->store->address.PHP_EOL);
        $this->printer->text(Formatter::header(
            'TID: '.$this->receiptPrinterData->transactionId,
            'MID: '.$this->receiptPrinterData->store->mid
        ).PHP_EOL);
        $this->printer->feed();
        // Print receipt title
        $this->printer->setEmphasis();
        $this->printer->text('RECEIPT'.PHP_EOL);
        $this->printer->setEmphasis(false);
        $this->printer->feed();

        // Print items
        $this->printer->setJustification(Printer::JUSTIFY_LEFT);
        foreach ($this->receiptPrinterData->items as $item) {
            $this->printer->text(Formatter::item($item));
        }
        $this->printer->feed();

        // subtotal
        $this->printer->setEmphasis();
        $this->printer->text(Formatter::summary('Subtotal', moneyAmountToFloat($subtotal)));
        $this->printer->setEmphasis(false);
        $this->printer->feed();

        // tax
        $this->printer->text(Formatter::summary('Tax', moneyAmountToFloat($tax)));
        $this->printer->feed(2);

        // grand total
        $this->printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
        $this->printer->text(Formatter::summary('TOTAL', moneyAmountToFloat($grandTotal), true));
        $this->printer->feed();
        $this->printer->selectPrintMode();

        if (filled($this->receiptPrinterData->qrCode)) {
            /** @var non-empty-string $json */
            $json = json_encode($this->receiptPrinterData->qrCode);
            $this->printer->qrCode($json, Printer::QR_ECLEVEL_L, 8);
        }

        // footer
        $this->printer->feed();
        $this->printer->setJustification(Printer::JUSTIFY_CENTER);
        $this->printer->text('Thank you for shopping!'.PHP_EOL);
        $this->printer->feed();

        // date
        $this->printer->text(self::now()->format('j F Y H:i:s'));
        $this->printer->feed(3);

        $this->printer->cut();

        //        $this->printer->openDrawer();
        $this->printer->close();

    }

    private static function now(): Carbon
    {
        $timezone = config()->string('app-default.timezone');

        if (Auth::check()) {
            /** @var Admin $admin */
            $admin = Auth::user();
            $timezone = $admin->timezone;
        }

        return now()->timezone($timezone);
    }
}
