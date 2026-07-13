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

namespace Domain\Shop\Order\Notifications;

use App\Filament\Admin\Resources\Shop\OrderResource as AdminOrderResource;
use App\Filament\Branch\Resources\Shop\OrderResource as BranchOrderResource;
use App\Jobs\QueueName;
use Domain\Access\Admin\Models\Admin;
use Domain\Shop\Order\Models\Order;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Support\Icons\Heroicon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Number;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\FilamentPermissionGenerateName;

class AdminOrderPlacedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Order $order,
    ) {
        $this->onQueue(QueueName::HIGH);
    }

    public function via(Admin $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(Admin $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(trans('New Order Received [:order]', ['order' => $this->order->receipt_number]))
            ->greeting(trans('Hello :admin!', ['admin' => $notifiable->name]))
            ->line(trans('You have received a new order.'))
            ->line(
                trans(
                    'Order created with price amount :amount',
                    [
                        'amount' => Number::currency(
                            $this->order->total_price,
                        ),
                    ],
                )
            )
            ->line(trans('Branch: :branch', ['branch' => $this->order->branch->name]))
            ->when(
                $this->orderResourceUrl($notifiable),
                function (MailMessage $mailMessage, string $url) {
                    $mailMessage
                        ->action(trans('View Order'), $url);
                }
            );
    }

    public function toDatabase(Admin $notifiable): array
    {
        return FilamentNotification::make()
            ->title(trans('New Order Received [:order].', ['order' => $this->order->receipt_number]))
            ->body(
                trans(
                    'Order created with price amount :amount.',
                    ['amount' => Number::currency($this->order->total_price)],
                )
            )
            ->icon(Heroicon::OutlinedShoppingBag)
            ->when(
                $this->orderResourceUrl($notifiable),
                function (FilamentNotification $notification, string $url) {
                    $notification->actions([
                        Action::make('view_order')
                            ->translateLabel()
                            ->button()
                            ->markAsRead()
                            ->url($url),
                    ]);
                }
            )
            ->getDatabaseMessage();
    }

    private function orderResourceUrl(Admin $admin): ?string
    {
        if ($admin->can(FilamentPermissionGenerateName::getPanelPermissionName('admin')) && $admin->can('order.view')) {
            Filament::setCurrentPanel(Filament::getPanel('admin'));

            return AdminOrderResource::getUrl('view', [$this->order]);
        }

        if (! $admin->can('order.view')) {
            return null;
        }

        Auth::setUser($admin);

        Filament::setTenant($this->order->branch);

        Filament::setCurrentPanel(Filament::getPanel('branch'));

        return BranchOrderResource::getUrl('view', [$this->order]);
    }
}
