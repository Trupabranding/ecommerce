{{-- SKU Pricing Matrix Component --}}
<div class="fi-in">
    <div class="rounded-lg border border-gray-200 bg-white overflow-hidden dark:border-gray-700 dark:bg-gray-900">
        {{-- Table Container with Responsive Scroll --}}
        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-800">
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">
                            <span class="flex items-center gap-2">
                                {{ __('Code') }}
                            </span>
                        </th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">
                            <span class="flex items-center gap-2">
                                {{ __('Price') }}
                            </span>
                        </th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">
                            {{ __('Total Stock') }}
                        </th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">
                            {{ __('Action') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($skus as $sku)
                        @php
                            $totalStock = $sku->skuStocks->sum('quantity');
                            $stockStatus = $totalStock > 10 ? 'good' : ($totalStock > 0 ? 'low' : 'critical');
                            $statusColors = [
                                'critical' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                                'low' => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
                                'good' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                            ];
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">
                                {{ $sku->code }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                                @if(is_object($sku->price))
                                    {{ $sku->price->format() }}
                                @else
                                    ${{ number_format($sku->price, 2) }}
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $statusColors[$stockStatus] }}">
                                    {{ $totalStock }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <a href="{{ $skuStockResource::getUrl('index') }}?sku_uuid={{ $sku->uuid }}"
                                   class="inline-flex items-center px-3 py-1 rounded-md bg-blue-100 text-blue-800 hover:bg-blue-200 dark:bg-blue-900 dark:text-blue-200 dark:hover:bg-blue-800 text-xs font-medium transition-colors">
                                    {{ __('Manage') }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                {{ __('No SKUs found') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Summary Row --}}
        @if($skus->isNotEmpty())
            <div class="bg-gray-50 dark:bg-gray-800 px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                <div class="grid grid-cols-4 gap-4">
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('Total SKUs') }}</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $skus->count() }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('Average Price') }}</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">
                            @php
                                $avgPrice = $skus->avg('price');
                            @endphp
                            ${{ number_format($avgPrice, 2) }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('Total Stock') }}</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ $skus->sum(fn ($sku) => $sku->skuStocks->sum('quantity')) }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('Stock Value') }}</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">
                            @php
                                $totalValue = $skus->sum(fn ($sku) => ($sku->price ?? 0) * $sku->skuStocks->sum('quantity'));
                            @endphp
                            ${{ number_format($totalValue, 2) }}
                        </p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
