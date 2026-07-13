<?php

declare(strict_types=1);

namespace App\Filament\Admin\Components;

use Filament\Support\Icons\Heroicon;
use Illuminate\View\Component;

class SettingsSidebar extends Component
{
    public function __construct(
        public ?string $activeCategory = null,
    ) {}

    public function getCategories(): array
    {
        return [
            [
                'id' => 'general',
                'label' => trans('General'),
                'icon' => Heroicon::OutlinedCog6Tooth,
                'url' => route('filament.admin.pages.settings.general'),
            ],
            [
                'id' => 'shop',
                'label' => trans('Shop'),
                'icon' => Heroicon::OutlinedShoppingBag,
                'url' => route('filament.admin.pages.settings.shop'),
                'children' => [
                    ['id' => 'products', 'label' => trans('Products')],
                    ['id' => 'categories', 'label' => trans('Categories')],
                    ['id' => 'inventory', 'label' => trans('Inventory')],
                ],
            ],
            [
                'id' => 'payments',
                'label' => trans('Payments'),
                'icon' => Heroicon::OutlinedCreditCard,
                'url' => route('filament.admin.pages.settings.payments'),
            ],
            [
                'id' => 'shipping',
                'label' => trans('Shipping'),
                'icon' => Heroicon::OutlinedTruck,
                'url' => route('filament.admin.pages.settings.shipping'),
            ],
            [
                'id' => 'notifications',
                'label' => trans('Notifications'),
                'icon' => Heroicon::OutlinedBell,
                'url' => route('filament.admin.pages.settings.notifications'),
            ],
            [
                'id' => 'security',
                'label' => trans('Security'),
                'icon' => Heroicon::OutlinedLockClosed,
                'url' => route('filament.admin.pages.settings.security'),
            ],
            [
                'id' => 'integrations',
                'label' => trans('Integrations'),
                'icon' => Heroicon::OutlinedPuzzle,
                'url' => route('filament.admin.pages.settings.integrations'),
            ],
            [
                'id' => 'billing',
                'label' => trans('Billing'),
                'icon' => Heroicon::OutlinedDocument,
                'url' => route('filament.admin.pages.settings.billing'),
            ],
        ];
    }

    public function isActive(string $categoryId): bool
    {
        return $this->activeCategory === $categoryId;
    }

    public function render()
    {
        return view('components.settings-sidebar', [
            'categories' => $this->getCategories(),
        ]);
    }
}
