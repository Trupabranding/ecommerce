<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Professional Header Section -->
        <div class="rounded-lg border border-slate-200 dark:border-slate-700 overflow-hidden bg-white dark:bg-slate-900">
            <!-- Cover Background -->
            <div class="h-32 bg-gradient-to-r from-zinc-100 to-slate-100 dark:from-zinc-900 dark:to-slate-800"></div>

            <!-- Profile Information -->
            <div class="px-6 pb-6 -mt-16 relative z-10">
                <div class="flex flex-col md:flex-row md:items-end gap-6">
                    <!-- Avatar -->
                    <div class="flex-shrink-0">
                        <div class="w-32 h-32 rounded-lg border-4 border-white dark:border-slate-900 bg-white dark:bg-slate-900 shadow-lg overflow-hidden">
                            @if($admin->avatar_path)
                                <img src="{{ asset('storage/' . $admin->avatar_path) }}" alt="{{ $admin->name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-zinc-200 dark:bg-zinc-700 flex items-center justify-center text-3xl font-bold text-zinc-600 dark:text-zinc-400">
                                    {{ strtoupper(substr($admin->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Profile Details -->
                    <div class="flex-1 pt-4">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <div>
                                <h1 class="text-3xl font-bold text-zinc-900 dark:text-white">{{ $admin->name }}</h1>
                                <p class="text-lg text-slate-600 dark:text-slate-400 mt-1">{{ $admin->email }}</p>

                                <!-- Role Badges -->
                                <div class="flex flex-wrap gap-2 mt-3">
                                    @forelse($admin->roles as $role)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200">
                                            {{ $role->name }}
                                        </span>
                                    @empty
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200">
                                            No roles assigned
                                        </span>
                                    @endforelse
                                </div>
                            </div>

                            <!-- Status Badge -->
                            <div class="flex flex-col items-start md:items-end gap-2">
                                @if($admin->email_verified_at)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">
                                        ✓ Email Verified
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-300">
                                        ⚠ Unverified
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Grid -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Account Created -->
            <div class="rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 p-6">
                <p class="text-sm font-medium text-slate-600 dark:text-slate-400">Account Created</p>
                <p class="text-2xl font-bold text-zinc-900 dark:text-white mt-2">{{ $admin->created_at->format('M d, Y') }}</p>
                <p class="text-xs text-slate-500 dark:text-slate-500 mt-1">{{ $admin->created_at->diffForHumans() }}</p>
            </div>

            <!-- Last Updated -->
            <div class="rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 p-6">
                <p class="text-sm font-medium text-slate-600 dark:text-slate-400">Last Updated</p>
                <p class="text-2xl font-bold text-zinc-900 dark:text-white mt-2">{{ $admin->updated_at->format('M d, Y') }}</p>
                <p class="text-xs text-slate-500 dark:text-slate-500 mt-1">{{ $admin->updated_at->diffForHumans() }}</p>
            </div>

            <!-- Email Status -->
            <div class="rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 p-6">
                <p class="text-sm font-medium text-slate-600 dark:text-slate-400">Email Status</p>
                <p class="text-2xl font-bold text-zinc-900 dark:text-white mt-2">
                    @if($admin->email_verified_at)
                        Verified
                    @else
                        Pending
                    @endif
                </p>
                <p class="text-xs text-slate-500 dark:text-slate-500 mt-1">
                    @if($admin->email_verified_at)
                        on {{ $admin->email_verified_at->format('M d, Y') }}
                    @else
                        Awaiting verification
                    @endif
                </p>
            </div>

            <!-- Timezone -->
            <div class="rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 p-6">
                <p class="text-sm font-medium text-slate-600 dark:text-slate-400">Timezone</p>
                <p class="text-2xl font-bold text-zinc-900 dark:text-white mt-2 truncate">{{ $admin->timezone ?? 'Not set' }}</p>
                <p class="text-xs text-slate-500 dark:text-slate-500 mt-1">Preferred timezone</p>
            </div>
        </div>

        <!-- Permissions Section -->
        <div class="rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 p-6">
            <h2 class="text-lg font-semibold text-zinc-900 dark:text-white mb-4">Permissions</h2>

            @if($admin->permissions->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($admin->permissions as $permission)
                        <div class="flex items-center gap-2 p-3 rounded-lg bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-sm font-medium text-zinc-900 dark:text-white">{{ $permission->name }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-slate-600 dark:text-slate-400">No direct permissions assigned. Access is controlled through roles.</p>
            @endif
        </div>

        <!-- Account Information Section -->
        <div class="rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 p-6">
            <h2 class="text-lg font-semibold text-zinc-900 dark:text-white mb-4">Account Information</h2>

            <dl class="space-y-4">
                <div class="flex justify-between items-center pb-4 border-b border-slate-200 dark:border-slate-700">
                    <dt class="text-sm font-medium text-slate-600 dark:text-slate-400">Admin ID</dt>
                    <dd class="text-sm font-mono text-zinc-900 dark:text-white">{{ $admin->uuid }}</dd>
                </div>

                <div class="flex justify-between items-center pb-4 border-b border-slate-200 dark:border-slate-700">
                    <dt class="text-sm font-medium text-slate-600 dark:text-slate-400">Email Address</dt>
                    <dd class="text-sm text-zinc-900 dark:text-white">{{ $admin->email }}</dd>
                </div>

                <div class="flex justify-between items-center pb-4 border-b border-slate-200 dark:border-slate-700">
                    <dt class="text-sm font-medium text-slate-600 dark:text-slate-400">Created At</dt>
                    <dd class="text-sm text-zinc-900 dark:text-white">{{ $admin->created_at->format('F d, Y H:i A') }}</dd>
                </div>

                <div class="flex justify-between items-center">
                    <dt class="text-sm font-medium text-slate-600 dark:text-slate-400">Last Updated</dt>
                    <dd class="text-sm text-zinc-900 dark:text-white">{{ $admin->updated_at->format('F d, Y H:i A') }}</dd>
                </div>
            </dl>
        </div>
    </div>
</x-filament-panels::page>
