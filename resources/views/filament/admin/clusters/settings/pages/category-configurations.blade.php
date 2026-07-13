<x-filament-panels::page>
    <x-slot name="heading">
        🏷️ Category Configurations
    </x-slot>

    <div class="space-y-6">
        <!-- Info Box -->
        <div class="rounded-xl border border-blue-200 dark:border-blue-900/50 bg-blue-50 dark:bg-blue-900/20 p-6">
            <div class="flex gap-4">
                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zm-11-1a1 1 0 11-2 0 1 1 0 012 0zM8 7a1 1 0 000 2h6a1 1 0 000-2H8zm0 3a1 1 0 000 2h6a1 1 0 000-2H8z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <h3 class="text-sm font-semibold text-blue-900 dark:text-blue-100">Category Configuration Management</h3>
                    <p class="text-sm text-blue-800 dark:text-blue-200 mt-2">Configure rules and settings specific to each product category including display options, product requirements, pricing, inventory, and SEO settings.</p>
                </div>
            </div>
        </div>

        <!-- Configuration Types -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Display Rules -->
            <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4 hover:shadow-md transition">
                <div class="flex items-start gap-3 mb-3">
                    <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4z"/>
                            <path fill-rule="evenodd" d="M3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zm11-3a1 1 0 10-2 0v2h-2a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900 dark:text-white text-sm">Display Rules</h4>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Templates, images, descriptions</p>
                    </div>
                </div>
            </div>

            <!-- Product Rules -->
            <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4 hover:shadow-md transition">
                <div class="flex items-start gap-3 mb-3">
                    <div class="p-2 bg-green-100 dark:bg-green-900/30 rounded-lg">
                        <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900 dark:text-white text-sm">Product Rules</h4>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Attributes, fields, images</p>
                    </div>
                </div>
            </div>

            <!-- Pricing Rules -->
            <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4 hover:shadow-md transition">
                <div class="flex items-start gap-3 mb-3">
                    <div class="p-2 bg-amber-100 dark:bg-amber-900/30 rounded-lg">
                        <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M8.16 5.314l4.897-1.346a1 1 0 0 1 .72 1.906L9.82 7.22a1 1 0 1 1-.522-1.906zm9.165-2.768a2 2 0 0 1-2.597-.087L7.293 14.707a2 2 0 1 0 2.828 2.828l7.275-7.275a2 2 0 0 1-.087-2.597l1.207-1.207a1 1 0 1 0-1.414-1.414l-1.207 1.207z"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900 dark:text-white text-sm">Pricing Rules</h4>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Tax, discounts, markups</p>
                    </div>
                </div>
            </div>

            <!-- Inventory Rules -->
            <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4 hover:shadow-md transition">
                <div class="flex items-start gap-3 mb-3">
                    <div class="p-2 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                        <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900 dark:text-white text-sm">Inventory Rules</h4>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">SKU tracking, backorders, levels</p>
                    </div>
                </div>
            </div>

            <!-- SEO Rules -->
            <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4 hover:shadow-md transition col-span-1 md:col-span-2">
                <div class="flex items-start gap-3 mb-3">
                    <div class="p-2 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg">
                        <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900 dark:text-white text-sm">SEO Rules</h4>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Meta templates, keywords, optimization</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Call-to-Action -->
        <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 p-6 text-center">
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">To configure individual categories, go to the Shop section and edit each category.</p>
            <a href="/admin/shop/categories" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Manage Categories
            </a>
        </div>
    </div>
</x-filament-panels::page>
