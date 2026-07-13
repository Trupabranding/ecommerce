{{-- Product Activity Timeline Component --}}
<div class="fi-in">
    <div class="relative">
        @if($activities->isNotEmpty())
            <div class="space-y-1">
                @foreach($activities as $activity)
                    @php
                        $actionType = $activity->event;
                        $actionColor = match($actionType) {
                            'created' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                            'updated' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                            'deleted' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                            default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200',
                        };
                        $actionIcon = match($actionType) {
                            'created' => 'heroicon-o-plus-circle',
                            'updated' => 'heroicon-o-pencil',
                            'deleted' => 'heroicon-o-trash',
                            default => 'heroicon-o-question-mark-circle',
                        };
                    @endphp

                    <div class="flex gap-4 pb-6 relative">
                        {{-- Timeline dot --}}
                        <div class="flex flex-col items-center">
                            <div class="w-10 h-10 rounded-full {{ $actionColor }} flex items-center justify-center flex-shrink-0 relative z-10">
                                @switch($actionType)
                                    @case('created')
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                        @break
                                    @case('updated')
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                        </svg>
                                        @break
                                    @case('deleted')
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                        @break
                                    @default
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zm-11-1a1 1 0 11-2 0 1 1 0 012 0zM8 9a1 1 0 100-2 1 1 0 000 2zm5 0a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                                        </svg>
                                @endswitch
                            </div>
                            @if(!$loop->last)
                                <div class="w-1 h-12 bg-gray-200 dark:bg-gray-700 mt-2"></div>
                            @endif
                        </div>

                        {{-- Activity content --}}
                        <div class="flex-1 pt-1">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                        {{ ucfirst($actionType) }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        @if($activity->causer)
                                            {{ __('by') }} {{ $activity->causer->name }}
                                        @else
                                            {{ __('System') }}
                                        @endif
                                    </p>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 flex-shrink-0 ml-2">
                                    {{ $activity->created_at->diffForHumans() }}
                                </p>
                            </div>

                            {{-- Changes details --}}
                            @if($activity->changes)
                                <div class="mt-2 text-xs text-gray-600 dark:text-gray-300 space-y-1">
                                    @foreach($activity->changes as $field => $change)
                                        @if(is_array($change) && isset($change['from'], $change['to']))
                                            <div class="flex items-start gap-2">
                                                <span class="font-medium text-gray-700 dark:text-gray-300">{{ Str::headline($field) }}:</span>
                                                <span class="text-gray-500 dark:text-gray-400 line-through">{{ $change['from'] ?? '-' }}</span>
                                                <span class="text-green-600 dark:text-green-400">→</span>
                                                <span class="text-green-600 dark:text-green-400">{{ $change['to'] ?? '-' }}</span>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="flex flex-col items-center justify-center py-12">
                <svg class="w-12 h-12 text-gray-400 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-gray-500 dark:text-gray-400">{{ __('No activities recorded yet') }}</p>
            </div>
        @endif
    </div>
</div>
