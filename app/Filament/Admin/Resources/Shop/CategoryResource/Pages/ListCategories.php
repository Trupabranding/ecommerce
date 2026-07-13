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

namespace App\Filament\Admin\Resources\Shop\CategoryResource\Pages;

use App\Filament\Admin\Resources\Shop\CategoryResource;
use App\Filament\Admin\Support\ListRecordsUseFastPaginate;
use Domain\Shop\Category\Exports\CategoryExporter;
use Domain\Shop\Category\Imports\CategoryImporter;
use Filament\Actions\CreateAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;
use Override;

class ListCategories extends ListRecords
{
    use ListRecordsUseFastPaginate;

    #[\Override]
    protected static string $resource = CategoryResource::class;

    #[Override]
    protected function getHeaderActions(): array
    {
        return [
            ImportAction::make()
                ->translateLabel()
                ->importer(CategoryImporter::class)
                ->authorize('import'),
            //                ->withActivityLog(),
            ExportAction::make()
                ->translateLabel()
                ->exporter(CategoryExporter::class)
                ->authorize('exportAny'),
            //                ->withActivityLog(),
            CreateAction::make()
                ->translateLabel(),
        ];
    }
}
