<?php

declare(strict_types=1);

namespace App\Jobs\Order;

use Domain\Shop\Order\Actions\CreateOrderInvoiceAction;
use Domain\Shop\Order\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateOrderInvoiceJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        private Order $order,
    ) {
        $this->onQueue('invoices');
    }

    public function handle(CreateOrderInvoiceAction $action): void
    {
        $action->execute($this->order);
    }
}
