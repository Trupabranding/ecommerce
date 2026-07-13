<x-filament-panels::page>
    <x-slot name="heading">
        Shipping Defaults
    </x-slot>

    <div class="space-y-6 max-w-4xl">
        <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-6">
            <h3 class="text-lg font-semibold mb-4">Shipping Configuration</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium">Default Shipping Providers</label>
                    <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">USPS, FedEx, UPS</p>
                </div>
                <div>
                    <label class="block text-sm font-medium">Default Method</label>
                    <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">Standard</p>
                </div>
                <div>
                    <label class="block text-sm font-medium">Free Shipping Threshold</label>
                    <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">$100.00</p>
                </div>
                <div>
                    <label class="block text-sm font-medium">Customer Pickup Available</label>
                    <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">No</p>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
