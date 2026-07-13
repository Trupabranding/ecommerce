<div class="settings-tabs">
    <div class="settings-tabs-container">
        <div class="settings-tabs-wrapper">
            @foreach($tabs as $tab)
                <a href="{{ $tab['url'] ?? '#' }}"
                   class="settings-tab @if($isActive($tab['id'])) active @endif">
                    @if(isset($tab['icon']))
                        <span class="settings-tab-icon">
                            @svg($tab['icon'], 'w-4 h-4')
                        </span>
                    @endif
                    <span class="settings-tab-label">{{ $tab['label'] }}</span>
                </a>
            @endforeach
        </div>
    </div>
</div>
