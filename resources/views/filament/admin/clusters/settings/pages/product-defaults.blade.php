<x-filament-panels::page>
    <x-slot name="heading">
        Product Defaults
    </x-slot>

    <div class="space-y-6 max-w-4xl">
        <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-6">
            <h3 class="text-lg font-semibold mb-6">Default Product Configuration</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium mb-2">Default Product Status</label>
                    <p class="text-gray-600 dark:text-gray-400">Draft</p>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Enable Customer Reviews</label>
                    <p class="text-gray-600 dark:text-gray-400">Yes</p>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Require Product SKU</label>
                    <p class="text-gray-600 dark:text-gray-400">No</p>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Default Visibility</label>
                    <p class="text-gray-600 dark:text-gray-400">Public</p>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Auto-Publish Products</label>
                    <p class="text-gray-600 dark:text-gray-400">No</p>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Product Image Upload Limit</label>
                    <p class="text-gray-600 dark:text-gray-400">10 MB</p>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
