<x-filament-panels::page>
    <x-slot name="heading">
        Integrations & APIs
    </x-slot>

    <div class="space-y-6 max-w-4xl">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Stripe -->
            <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="text-2xl">💳</div>
                    <h3 class="text-lg font-semibold">Stripe</h3>
                </div>
                <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">Payment processing integration</p>
                <button class="px-4 py-2 bg-blue-600 text-white rounded text-sm hover:bg-blue-700">Configure</button>
            </div>

            <!-- Mailchimp -->
            <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="text-2xl">📧</div>
                    <h3 class="text-lg font-semibold">Mailchimp</h3>
                </div>
                <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">Email marketing automation</p>
                <button class="px-4 py-2 bg-blue-600 text-white rounded text-sm hover:bg-blue-700">Configure</button>
            </div>

            <!-- SendGrid -->
            <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="text-2xl">✉️</div>
                    <h3 class="text-lg font-semibold">SendGrid</h3>
                </div>
                <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">Email delivery service</p>
                <button class="px-4 py-2 bg-blue-600 text-white rounded text-sm hover:bg-blue-700">Configure</button>
            </div>

            <!-- Slack -->
            <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="text-2xl">🔔</div>
                    <h3 class="text-lg font-semibold">Slack</h3>
                </div>
                <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">Notifications & alerts</p>
                <button class="px-4 py-2 bg-blue-600 text-white rounded text-sm hover:bg-blue-700">Configure</button>
            </div>
        </div>

        <!-- API Settings -->
        <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-6">
            <h3 class="text-lg font-semibold mb-4">API Settings</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-2">Rate Limit</label>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">60 requests per minute</p>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
