<x-filament-panels::page>
    <x-slot name="heading">
        🚚 Shipping Defaults
    </x-slot>

    <div class="space-y-6">
        <!-- Shipping Methods -->
        <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm overflow-hidden">
            <div class="bg-linear-to-r from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-800/20 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                    </svg>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">Shipping Methods</h3>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6">
                <div class="p-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                    <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide mb-2">Providers</p>
                    <div class="flex flex-wrap gap-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300">USPS</span>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300">FedEx</span>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300">UPS</span>
                    </div>
                </div>
                <div class="p-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                    <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide mb-1">Default Method</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white">Standard</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">3-5 business days</p>
                </div>
            </div>
        </div>

        <!-- Shipping Policies -->
        <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm overflow-hidden">
            <div class="bg-linear-to-r from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 10.5a1.5 1.5 0 113 0v-1a1.5 1.5 0 01-3 0v1zM6 10.333v5.43a2 2 0 001.106 1.79l.05.025A4 4 0 008.943 18h5.416a2 2 0 001.962-1.608l1.2-6A2 2 0 0015.56 8H12V4a2 2 0 00-2-2 1 1 0 00-1 1v.667a4 4 0 01-.8 2.4L6.256 8H6z"/>
                    </svg>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">Shipping Policies</h3>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6">
                <div class="p-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                    <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide mb-1">Free Shipping Threshold</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white">$100.00</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Minimum order for free shipping</p>
                </div>
                <div class="p-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                    <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide mb-1">Pickup Available</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300">Disabled</span>
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">In-store pickup option</p>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
