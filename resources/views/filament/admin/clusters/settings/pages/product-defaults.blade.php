<x-filament-panels::page>
    <x-slot name="heading">
        📦 Product Defaults
    </x-slot>

    <div class="space-y-6">
        <!-- Status & Visibility Settings -->
        <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm overflow-hidden">
            <div class="bg-gradient-to-r from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a1 1 0 001 1h12a1 1 0 001-1V6a2 2 0 00-2-2H4zm12 12H4c-1.1 0-2-.9-2-2v-4a1 1 0 00-1-1H0v6a2 2 0 002 2h12.1l3.9 3.9a1 1 0 001.4-1.4l-3.9-3.9z" clip-rule="evenodd"/>
                    </svg>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">Status & Visibility</h3>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 p-6">
                <div class="p-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                    <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide mb-1">Default Status</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white">Draft</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Status assigned to new products</p>
                </div>
                <div class="p-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                    <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide mb-1">Default Visibility</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white">Public</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Visibility for new products</p>
                </div>
                <div class="p-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                    <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide mb-1">Auto-Publish</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white">Disabled</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Auto-publish on creation</p>
                </div>
            </div>
        </div>

        <!-- Product Requirements -->
        <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm overflow-hidden">
            <div class="bg-gradient-to-r from-amber-50 to-amber-100 dark:from-amber-900/20 dark:to-amber-800/20 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5 2a1 1 0 011 1v1h1a1 1 0 000-2H5a1 1 0 00-1 1v1H3a1 1 0 000 2h1v1a1 1 0 102 0V4h1a1 1 0 100-2H6V2a1 1 0 01-1-1zm0 4a2 2 0 11-4 0 2 2 0 014 0z" clip-rule="evenodd"/>
                    </svg>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">Product Requirements</h3>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 p-6">
                <div class="p-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                    <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide mb-1">Require SKU</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white">No</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">SKU field mandatory</p>
                </div>
                <div class="p-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                    <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide mb-1">Reviews Enabled</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white">Yes</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Customer product reviews</p>
                </div>
                <div class="p-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                    <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide mb-1">Image Limit</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white">10 MB</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Max upload per image</p>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
