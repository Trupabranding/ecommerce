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

namespace App\Http\Requests\API\Shop\Order;

use Domain\Shop\Branch\Models\Branch;
use Domain\Shop\OperationHour\Actions\GetOpeningHoursByBranchAction;
use Domain\Shop\Order\Enums\ClaimType;
use Domain\Shop\Order\Enums\OrderPaymentMethod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'claim_type' => [
                'required',
                Rule::enum(ClaimType::class),
            ],
            'payment_method' => ['required', Rule::enum(OrderPaymentMethod::class)],
            'notes' => 'nullable|string|min:5',
            'claim_at' => [
                'required_if:claim_type,'.ClaimType::delivery->value,
                'prohibited_if:claim_type,'.ClaimType::pickup->value,
                'date_format:Y-m-d H:i',
                'after:now',
                function (string $attribute, string $value, callable $fail) {

                    /** @var Branch $branch */
                    $branch = $this->route('enabledBranch');

                    $claimType = $this->enum('claim_type', ClaimType::class);

                    if (! $branch->is_operation_hours_enabled || $claimType === null) {
                        return;
                    }

                    match ($claimType) {
                        ClaimType::delivery => $branch->load('operationHoursOnline'),
                        ClaimType::pickup => $branch->load('operationHoursInStore'),
                    };

                    $customer = customer_auth();

                    $datetime = now()->parse($value, $customer->timezone);

                    $openingHours = app(GetOpeningHoursByBranchAction::class)
                        ->execute($branch, $claimType->operationHourType());

                    if ($openingHours->isClosedAt($datetime)) {

                        $fail(trans(':Claim_type datetime `:datetime` in not available.', [
                            'claim_type' => $claimType->value,
                            'datetime' => $datetime->toString(),
                        ]));

                    }

                },
            ],
        ];
    }
}
