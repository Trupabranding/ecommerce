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

namespace Domain\Shop\Order\Notifications;

use App\Jobs\QueueName;
use App\Settings\SiteSettings;
use Domain\Shop\Customer\Models\Customer;
use Domain\Shop\Order\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Number;

class CustomerOrderPlacedNotification extends Notification implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function __construct(private readonly Order $order)
    {
        $this->onQueue(QueueName::HIGH);
    }

    public function via(Customer $notifiable): array
    {
        return ['mail'];
    }

    /**
     * @throws \Exception
     */
    public function toMail(Customer $notifiable): MailMessage
    {
        $siteSetting = app(SiteSettings::class);

        /** @var \Domain\Shop\Order\Models\OrderInvoice $invoice */
        $invoice = $this->order->orderInvoices->first();

        return (new MailMessage)
            ->subject(trans(':site Order Confirmation', ['site' => $siteSetting->name]))
            ->greeting(trans('Hello :customer!', ['customer' => $notifiable->full_name]))
            ->line(trans('Your order [:order] has been submitted and is now processing.', ['order' => $this->order->receipt_number]))
            ->line(
                trans(
                    'Order created with price amount :amount.',
                    [
                        'amount' => Number::currency(
                            $this->order->total_price,
                            config()->string('app-default.currency')
                        ),
                    ],
                )
            )
            ->line(trans('Branch: :branch', ['branch' => $this->order->branch->name]))
            ->attachData(
                $invoice->readStream(),
                $invoice->file_name
            );
    }
}
