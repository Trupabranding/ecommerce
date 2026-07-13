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

use App\Filament\Admin\Resources\Shop\ProductResource as AdminProductResource;
use App\Filament\Branch\Resources\Shop\ProductResource as BranchProductResource;
use App\Jobs\QueueName;
use Domain\Access\Admin\Models\Admin;
use Domain\Shop\Order\Models\Order;
use Domain\Shop\Stock\Models\SkuStock;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Support\Icons\Heroicon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\FilamentPermissionGenerateName;

class StockWarningNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Order $order,
        private readonly SkuStock $skuStock,
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
            ->subject(trans('Sku [:sku_code] Stock Warning', ['sku_code' => $this->skuStock->sku->code]))
            ->greeting(trans('Hello :admin!', ['admin' => $notifiable->name]))
            ->line(
                trans(
                    'Sku [:sku_code] has only [:stock_count] available stock, after order [:order] created.',
                    [
                        'sku_code' => $this->skuStock->sku->code,
                        'stock_count' => $this->skuStock->count ?? 'n/a',
                        'order' => $this->order->receipt_number,
                    ]
                )
            )
            ->line(trans('Branch: :branch', ['branch' => $this->order->branch->name]))
            ->when(
                $this->orderProductUrl($notifiable),
                function (MailMessage $mailMessage, string $url) {
                    $mailMessage
                        ->action(trans('View Product'), $url);
                }
            );
    }

    public function toDatabase(Admin $notifiable): array
    {
        return FilamentNotification::make()
            ->title(trans('Sku [:sku_code] Stock Warning.', ['sku_code' => $this->skuStock->sku->code]))
            ->body(
                trans(
                    'Sku [:sku_code] has only [:stock_count] available stock, after order [:order] created.',
                    [
                        'sku_code' => $this->skuStock->sku->code,
                        'stock_count' => $this->skuStock->count ?? 'n/a',
                        'order' => $this->order->receipt_number,
                    ]
                )
            )
            ->icon(Heroicon::OutlinedExclamationCircle)
            ->when(
                $this->orderProductUrl($notifiable),
                function (FilamentNotification $notification, string $url) {
                    $notification
                        ->actions([
                            Action::make('view_sku_stock')
                                ->translateLabel()
                                ->button()
                                ->markAsRead()
                                ->url($url),
                        ]);
                }
            )
            ->getDatabaseMessage();
    }

    private function orderProductUrl(Admin $admin): ?string
    {
        if ($admin->can(FilamentPermissionGenerateName::getPanelPermissionName('admin')) && $admin->can('product.update')) {
            Filament::setCurrentPanel(Filament::getPanel('admin'));

            return AdminProductResource::getUrl('edit', [$this->skuStock->sku->product]);
        }

        if (! $admin->can('product.update')) {
            return null;
        }

        Auth::setUser($admin);

        Filament::setTenant($this->order->branch);

        Filament::setCurrentPanel(Filament::getPanel('branch'));

        return BranchProductResource::getUrl('edit', [$this->skuStock->sku->product]);
    }
}
