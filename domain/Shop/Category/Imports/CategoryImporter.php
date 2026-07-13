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

namespace Domain\Shop\Category\Imports;

use App\Jobs\QueueName;
use Domain\Shop\Category\Models\Category;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Str;

class CategoryImporter extends Importer
{
    #[\Override]
    protected static ?string $model = Category::class;

    #[\Override]
    public function getJobQueue(): ?string
    {
        return QueueName::IMPORTS->value;
    }

    #[\Override]
    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'max:255'])
                ->exampleHeader('Name')
                ->example('Category A'),

            ImportColumn::make('parent')
                ->relationship(resolveUsing: 'name')
                ->rules(['nullable', 'max:255'])
                ->exampleHeader('Parent Name')
                ->example('Category B'),

            ImportColumn::make('description')
                ->rules(['nullable', 'max:255', 'string'])
                ->exampleHeader('Description')
                ->example('This is the description for Category A.'),

            ImportColumn::make('is_visible')
                ->requiredMapping()
                ->boolean()
                ->rules(['required', 'boolean'])
                ->exampleHeader('Visible')
                ->example('yes'),
        ];
    }

    #[\Override]
    public function resolveRecord(): ?Category
    {
        return Category::firstOrNew([
            'name' => $this->data['name'],
        ]);
    }

    #[\Override]
    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your category import has completed and '.
            number_format($import->successful_rows).' '.Str::of('row')
                ->plural($import->successful_rows).' imported.';

        if (($failedRowsCount = $import->getFailedRowsCount()) > 0) {
            $body .= ' '.number_format($failedRowsCount).' '.
                Str::of('row')->plural($failedRowsCount).' failed to import.';
        }

        return $body;
    }
}
