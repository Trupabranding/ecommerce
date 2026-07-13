<?php

declare(strict_types=1);

namespace App\Filament\Admin\Components;

use Illuminate\View\Component;

class SettingsSection extends Component
{
    public function __construct(
        public string $title = '',
        public ?string $description = null,
        public string $class = '',
    ) {}

    public function render()
    {
        return view('components.settings-section');
    }
}
