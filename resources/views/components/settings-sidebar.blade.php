<nav class="settings-sidebar">
    <div class="settings-sidebar-inner">
        <div class="settings-sidebar-header">
            <h3 class="settings-sidebar-title">{{ trans('Settings') }}</h3>
        </div>

        <ul class="settings-sidebar-menu">
            @foreach($categories as $category)
                <li class="settings-menu-item">
                    <a href="{{ $category['url'] }}"
                       class="settings-menu-link @if($isActive($category['id'])) active @endif">
                        <span class="settings-menu-icon">
                            @svg($category['icon'], 'w-5 h-5')
                        </span>
                        <span class="settings-menu-label">{{ $category['label'] }}</span>
                    </a>

                    @if(isset($category['children']) && !empty($category['children']))
                        <ul class="settings-submenu">
                            @foreach($category['children'] as $child)
                                <li class="settings-submenu-item">
                                    <a href="#" class="settings-submenu-link">
                                        {{ $child['label'] }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </li>
            @endforeach
        </ul>

        <div class="settings-sidebar-footer">
            <a href="{{ route('filament.admin.pages.dashboard') }}"
               class="settings-back-link">
                <span class="settings-back-icon">
                    @svg(Heroicon::OutlinedArrowLeft, 'w-4 h-4')
                </span>
                <span>{{ trans('Back to main menu') }}</span>
            </a>
        </div>
    </div>
</nav>
