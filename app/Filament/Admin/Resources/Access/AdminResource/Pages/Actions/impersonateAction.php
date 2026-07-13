<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Access\AdminResource\Pages\Actions;

use Domain\Access\Admin\Models\Admin;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Override;
use STS\FilamentImpersonate\Actions\Impersonate;
use STS\FilamentImpersonate\Facades\Impersonation;

class impersonateAction extends Impersonate
{
    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->redirectTo(function (Admin $record, self $action) {
                if (! config()->boolean('app-default.branch_feature_enabled')) {
                    return Filament::getUrl();
                }

                if ($record->isBranch()) {
                    $branch = $record->branches->first();

                    if (filled($branch)) {
                        return route('filament.branch.pages.main-dashboard', $branch);
                    }

                    Impersonation::leave();

                    Notification::make()
                        ->title(trans('Admin has no branch attached.'))
                        ->danger()
                        ->send();

                    $action->halt(shouldRollBackDatabaseTransaction: true);
                }

                return Filament::getUrl();
            });
    }
}
