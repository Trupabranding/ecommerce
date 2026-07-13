<?php

/*
 * Copyright (c) 2023 Lloric Mayuga Garcia
 * All rights reserved.
 *
 * 1. Usage Permissions
 *    This software is licensed exclusively to Lloric Mayuga Garcia. The following restrictions apply:
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
 * 📩 For inquiries, contact: lloricode@gmail.com
 * 🌐 Official Website: https://github.com/lloricode
 * 🛒 Purchase Here: https://lloricode.gumroad.com/l/laravel-filament-point-of-sale
 */

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Access\ActivityResource\RelationManagers;

use App\Filament\Admin\Resources\Access\ActivityResource;
use Exception;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Override;

class ActivitiesRelationManager extends RelationManager
{
    #[\Override]
    protected static string $relationship = 'activities';

    #[\Override]
    protected static ?string $recordTitleAttribute = 'description';

    #[Override]
    public function infolist(Schema $schema): Schema
    {
        return ActivityResource::infolist($schema);
    }

    //    public function form(Form $form): Form
    //    {
    //        return ActivityResource::form($form);
    //    }

    /** @throws Exception */
    #[Override]
    public function table(Table $table): Table
    {
        return ActivityResource::table($table);
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
