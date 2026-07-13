<?php

/*
 * Copyright (c) 2026 Trupa Technologies
 * All rights reserved.
 *
 * Developed by Boncanca Collins
 * GitHub: @iamtomc, @boncanca
 * Organization: trupabranding
 *
 * 1. Usage Permissions
 *    This software is proprietary to Trupa Technologies. The following restrictions apply:
 *    ✅ Allowed:
 *
 *     - Private use within the authorized organization.
 *     - Internal modifications.
 *     🚫 Not Allowed:
 *
 *     - Redistribution, sublicensing, or public sharing.
 *     - Commercial use outside of the authorized organization.
 * 2. Disclaimer of Warranty
 *    This software is provided "as is", without any warranty of any kind, express or implied, including but not limited to:
 *
 *     - Merchantability
 *     - Fitness for a particular purpose
 *     - Non-infringement
 * 3. Liability Limitation
 *    Under no circumstances shall the author(s) or copyright holders be liable for any claims, damages, or other liabilities arising from the use of this software.
 *
 * 4. Legal Enforcement
 *    Unauthorized use, distribution, or modification is strictly prohibited and may result in legal consequences.
 *
 * 📩 For inquiries, contact: hello@trupabranding.com
 * 🌐 Official Website: https://trupabranding.com
 * 📱 GitHub Organization: https://github.com/trupabranding
 */

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Access\ActivityResource\RelationManagers;

use App\Filament\Admin\Resources\Access\ActivityResource;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Override;

class ActionsRelationManager extends RelationManager
{
    #[\Override]
    protected static string $relationship = 'actions';

    #[\Override]
    protected static ?string $recordTitleAttribute = 'id';

    #[Override]
    public function form(Schema $schema): Schema
    {
        return ActivityResource::form($schema);
    }

    #[Override]
    public function table(Table $table): Table
    {
        return (new ActivitiesRelationManager)->table($table);
    }

    #[Override]
    protected function canCreate(): bool
    {
        return false;
    }

    #[Override]
    protected function canEdit(Model $record): bool
    {
        return false;
    }

    #[Override]
    protected function canDelete(Model $record): bool
    {
        return false;
    }
}
