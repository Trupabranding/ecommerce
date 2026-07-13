<x-filament-panels::page>
    <x-slot name="heading">
        🔌 Integrations & APIs
    </x-slot>

    <div class="space-y-6">
        <!-- Payment Integrations -->
        <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm overflow-hidden">
            <div class="bg-linear-to-r from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M4 4a2 2 0 00-2 2v4a1 1 0 001 1h12a1 1 0 001-1V6a2 2 0 00-2-2H4zm12 12H4c-1.1 0-2-.9-2-2v-4a1 1 0 00-1-1H0v6a2 2 0 002 2h12.1l3.9 3.9a1 1 0 001.4-1.4l-3.9-3.9z"/>
                    </svg>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">Payment Processing</h3>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-6">
                <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800/50 transition">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">💳 Stripe</p>
                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Payment processing</p>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">Active</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Email & Marketing -->
        <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm overflow-hidden">
            <div class="bg-linear-to-r from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                    </svg>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">Email & Marketing</h3>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-6">
                <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800/50 transition">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">📧 Mailchimp</p>
                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Email marketing</p>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300">Inactive</span>
                    </div>
                </div>
                <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800/50 transition">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">✉️ SendGrid</p>
                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Email delivery</p>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300">Inactive</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notifications & Chat -->
        <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm overflow-hidden">
            <div class="bg-linear-to-r from-indigo-50 to-indigo-100 dark:from-indigo-900/20 dark:to-indigo-800/20 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 5a2 2 0 012-2h12a2 2 0 012 2v10a2 2 0 01-2 2H4a2 2 0 01-2-2V5z"/>
                    </svg>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">Notifications & Chat</h3>
                </div>
            </div>
            <div class="grid grid-cols-1 gap-4 p-6">
                <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800/50 transition">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">🔔 Slack</p>
                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Real-time notifications and team chat integration</p>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300">Inactive</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- API Settings -->
        <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm overflow-hidden">
            <div class="bg-linear-to-r from-orange-50 to-orange-100 dark:from-orange-900/20 dark:to-orange-800/20 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-orange-600 dark:text-orange-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 6a3 3 0 013-3h10a1 1 0 00-.82-.58l-8-1A1 1 0 005 4v12a1 1 0 001 1h6a1 1 0 001-1V6a3 3 0 01-3-3zm0 0h3a3 3 0 013-3h4a1 1 0 011 1v1a1 1 0 11-2 0v-.5a1 1 0 00-1-.5H6a1 1 0 00-1 .5v.5a1 1 0 11-2 0V3a1 1 0 00-1 1z" clip-rule="evenodd"/>
                    </svg>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">API Configuration</h3>
                </div>
            </div>
            <div class="p-6">
                <div class="p-4 bg-orange-50 dark:bg-orange-900/20 rounded-lg border border-orange-200 dark:border-orange-800">
                    <p class="text-xs font-semibold text-orange-600 dark:text-orange-400 uppercase tracking-wide mb-1">Rate Limit</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">60</p>
                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-2">Requests per minute</p>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
