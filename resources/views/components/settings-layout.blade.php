<div class="settings-layout">
    <aside class="settings-layout-sidebar">
        <x-settings-sidebar :activeCategory="$activeCategory" />
    </aside>

    <main class="settings-layout-main">
        <div class="settings-layout-header">
            <nav class="settings-breadcrumb" aria-label="breadcrumb">
                <ol class="settings-breadcrumb-list">
                    <li class="settings-breadcrumb-item">
                        <a href="{{ route('filament.admin.pages.dashboard') }}" class="settings-breadcrumb-link">
                            {{ trans('Settings') }}
                        </a>
                    </li>
                    @foreach($breadcrumbs ?? [] as $breadcrumb)
                        <li class="settings-breadcrumb-item">
                            @if($loop->last)
                                <span class="settings-breadcrumb-current">{{ $breadcrumb['label'] }}</span>
                            @else
                                <a href="{{ $breadcrumb['url'] }}" class="settings-breadcrumb-link">
                                    {{ $breadcrumb['label'] }}
                                </a>
                            @endif
                        </li>
                    @endforeach
                </ol>
            </nav>
        </div>

        @if($tabs ?? null)
            <div class="settings-layout-tabs">
                <x-settings-tabs :tabs="$tabs" :activeTab="$activeTab" />
            </div>
        @endif

        <div class="settings-layout-content">
            {{ $slot }}
        </div>
    </main>
</div>
