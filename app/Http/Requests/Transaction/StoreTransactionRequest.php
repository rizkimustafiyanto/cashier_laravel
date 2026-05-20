<?php

declare(strict_types=1);

namespace App\Http\Requests\Transaction;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            /**
             * Patient
             */

            'patient_name' => ['required', 'string', 'max:70'],

            /**
             * Insurance
             */
            'insurance_id' => ['nullable', 'string', 'max:100'],

            /**
             * Items
             */

            'items' => ['required', 'array', 'min:1'],

            'items.*.procedure_id' => ['required', 'string', 'max:100'],
            'items.*.procedure_name' => ['required', 'string', 'max:100'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */

    public function messages(): array
    {
        return [
            'patient_name.required' => 'Patient name is required.',
            'patient_name.string' => 'Patient name must be a string.',
            'patient_name.max' => 'Patient name must not exceed 70 characters.',

            'insurance_id.string' => 'Insurance ID must be a string.',
            'insurance_id.max' => 'Insurance ID must not exceed 100 characters.',

            'items.required' => 'At least one item is required.',
            'items.array' => 'Items must be an array.',
            'items.min' => 'At least one item is required.',

            'items.*.procedure_id.required' => 'Procedure ID is required for each item.',
            'items.*.procedure_id.string' => 'Procedure ID must be a string.',
            'items.*.procedure_id.max' => 'Procedure ID must not exceed 100 characters.',

            'items.*.procedure_name.required' => 'Procedure name is required for each item.',
            'items.*.procedure_name.string' => 'Procedure name must be a string.',
            'items.*.procedure_name.max' => 'Procedure name must not exceed 100 characters.',
        ];
    }
}
