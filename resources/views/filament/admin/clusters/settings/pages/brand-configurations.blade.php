<x-filament-panels::page>
    <x-slot name="heading">
        🏢 Brand Configurations
    </x-slot>

    <div class="space-y-6">
        <!-- Info Box -->
        <div class="rounded-xl border border-amber-200 dark:border-amber-900/50 bg-amber-50 dark:bg-amber-900/20 p-6">
            <div class="flex gap-4">
                <svg class="w-6 h-6 text-amber-600 dark:text-amber-400 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.5 2A1.5 1.5 0 003 3.5v13A1.5 1.5 0 004.5 18h11a1.5 1.5 0 001.5-1.5V3.5A1.5 1.5 0 0015.5 2h-11zm4 9a2 2 0 11-4 0 2 2 0 014 0z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <h3 class="text-sm font-semibold text-amber-900 dark:text-amber-100">Brand Configuration Management</h3>
                    <p class="text-sm text-amber-800 dark:text-amber-200 mt-2">Configure display settings, pricing rules, and product requirements specific to each brand in your catalog.</p>
                </div>
            </div>
        </div>

        <!-- Configuration Types -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Display Settings -->
            <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4 hover:shadow-md transition">
                <div class="flex items-start gap-3 mb-3">
                    <div class="p-2 bg-amber-100 dark:bg-amber-900/30 rounded-lg">
                        <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4z"/>
                            <path fill-rule="evenodd" d="M3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zm11-3a1 1 0 10-2 0v2h-2a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900 dark:text-white text-sm">Display Settings</h4>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Color, logo, story</p>
                    </div>
                </div>
            </div>

            <!-- Pricing Rules -->
            <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4 hover:shadow-md transition">
                <div class="flex items-start gap-3 mb-3">
                    <div class="p-2 bg-green-100 dark:bg-green-900/30 rounded-lg">
                        <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M8.16 5.314l4.897-1.346a1 1 0 0 1 .72 1.906L9.82 7.22a1 1 0 1 1-.522-1.906zm9.165-2.768a2 2 0 0 1-2.597-.087L7.293 14.707a2 2 0 1 0 2.828 2.828l7.275-7.275a2 2 0 0 1-.087-2.597l1.207-1.207a1 1 0 1 0-1.414-1.414l-1.207 1.207z"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900 dark:text-white text-sm">Pricing Rules</h4>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Margins, discounts, markup</p>
                    </div>
                </div>
            </div>

            <!-- Requirements -->
            <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4 hover:shadow-md transition">
                <div class="flex items-start gap-3 mb-3">
                    <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900 dark:text-white text-sm">Requirements</h4>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Certification, variants, tier</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Brand Features Grid -->
        <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 overflow-hidden">
            <div class="bg-linear-to-r from-amber-50 to-orange-50 dark:from-amber-900/20 dark:to-orange-900/20 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Available Features</h3>
            </div>
            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                <div class="p-4 flex items-start gap-3 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition">
                    <svg class="w-5 h-5 text-green-600 dark:text-green-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">Brand Color & Logo Management</p>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Set unique brand colors and logos for storefront display</p>
                    </div>
                </div>
                <div class="p-4 flex items-start gap-3 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition">
                    <svg class="w-5 h-5 text-green-600 dark:text-green-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">Pricing Rules & Margins</p>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Control minimum margins and maximum discount percentages per brand</p>
                    </div>
                </div>
                <div class="p-4 flex items-start gap-3 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition">
                    <svg class="w-5 h-5 text-green-600 dark:text-green-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">Compliance Requirements</p>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Define certifications and quality tier requirements for each brand</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Call-to-Action -->
        <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 p-6 text-center">
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">To configure individual brands, go to the Shop section and edit each brand.</p>
            <a href="/admin/shop/brands" class="inline-flex items-center px-4 py-2 bg-amber-600 text-white rounded-lg text-sm font-medium hover:bg-amber-700 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Manage Brands
            </a>
        </div>
    </div>
</x-filament-panels::page>
