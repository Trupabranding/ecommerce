<?php

declare(strict_types=1);

namespace App\Filament\Admin\Components;

use Illuminate\View\Component;

class SettingsTabs extends Component
{
    /**
     * @param  array<array{id: string, label: string, url?: string, icon?: string}>  $tabs
     */
    public function __construct(
        public array $tabs = [],
        public ?string $activeTab = null,
    ) {}

    public function isActive(string $tabId): bool
    {
        return $this->activeTab === $tabId;
    }

    public function render()
    {
        return view('components.settings-tabs', [
            'tabs' => $this->tabs,
            'isActive' => fn (string $id) => $this->isActive($id),
        ]);
    }
}
