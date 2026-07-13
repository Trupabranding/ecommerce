<div class="settings-section {{ $class }}">
    <div class="settings-section-header">
        <h3 class="settings-section-title">{{ $title }}</h3>
        @if($description)
            <p class="settings-section-description">{{ $description }}</p>
        @endif
    </div>

    <div class="settings-section-content">
        {{ $slot }}
    </div>
</div>
