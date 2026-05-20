<?php

declare(strict_types=1);

namespace App\Rules\Voucher;

use Carbon\Carbon;

class VoucherValidationRules
{
    public static function createRules(): array
    {
        return [
            'insuranceId' => ['required', 'string', 'max:255'],
            'insuranceName' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:percentage,fixed'],
            'value' => ['required', 'numeric', 'min:0.01'],
            'maxDiscount' => ['nullable', 'numeric', 'min:0'],
            'startDate' => ['required', 'date'],
            'endDate' => ['required', 'date', 'after_or_equal:startDate'],
            'isActive' => ['boolean'],
        ];
    }

    public static function updateRules(): array
    {
        return self::createRules();
    }

    public static function messages(): array
    {
        return [
            'insuranceId.required' => 'Insurance selection is required',
            'insuranceName.required' => 'Insurance name is required',
            'type.required' => 'Discount type is required',
            'type.in' => 'Discount type must be either percentage or fixed amount',
            'value.required' => 'Discount value is required',
            'value.numeric' => 'Discount value must be a number',
            'value.min' => 'Discount value must be greater than 0',
            'maxDiscount.numeric' => 'Max discount must be a number',
            'maxDiscount.min' => 'Max discount cannot be negative',
            'startDate.required' => 'Start date is required',
            'startDate.date' => 'Start date must be a valid date',
            'endDate.required' => 'End date is required',
            'endDate.date' => 'End date must be a valid date',
            'endDate.after_or_equal' => 'End date must be after or equal to start date',
            'isActive.boolean' => 'Active status must be true or false',
        ];
    }

    public static function attributes(): array
    {
        return [
            'insuranceId' => 'Insurance',
            'insuranceName' => 'Insurance Name',
            'type' => 'Discount Type',
            'value' => 'Discount Value',
            'maxDiscount' => 'Maximum Discount',
            'startDate' => 'Start Date',
            'endDate' => 'End Date',
            'isActive' => 'Active',
        ];
    }
}
