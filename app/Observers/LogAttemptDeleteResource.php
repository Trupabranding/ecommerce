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

namespace App\Observers;

use App\Exceptions\DeletingResourceException;
use Exception;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Support\Exceptions\Halt;
use Illuminate\Database\Eloquent\Model;

trait LogAttemptDeleteResource
{
    /**
     * @throws Halt
     */
    private static function abortThenLogAttemptDelete(
        Model $model,
        string $errorMessage,
        array $properties,
    ): void {

        foreach ($properties as $property) {
            if (is_array($property)) {
                throw new Exception('Properties value must not array.');
            }
        }

        activity('admin')
            ->performedOn($model)
            ->event('deleting-attempt')
            ->withProperties($properties)
            ->log('Attempted to delete resource.');

        if (Filament::isServing()) {

            Notification::make()
                ->title(trans('Failed to delete resource.'))
                ->body($errorMessage)
                ->persistent()
                ->warning()
                ->send();

            throw (new Halt)->rollBackDatabaseTransaction();
        }

        throw DeletingResourceException::cannotDeleteParentResourceWhileHasChildResource($errorMessage);
    }

    /**
     * @param  non-empty-string  $relationshipName
     *
     * @throws Halt
     */
    private static function abortThenLogAttemptDeleteRelationCount(
        Model $model,
        string $errorMessage,
        string $relationshipName, // TODO: check relationship string.
        int $relationshipCount,
    ): void {
        if ($relationshipCount < 1) {
            throw new Exception('$relationshipCount should not below 1.');
        }

        self::abortThenLogAttemptDelete($model, $errorMessage, [
            'relationship_name' => $relationshipName,
            'relationship_count' => $relationshipCount,
        ]);

    }
}
