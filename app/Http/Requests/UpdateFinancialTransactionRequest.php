<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFinancialTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization will be handled by middleware
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'transaction_date' => ['sometimes', 'required', 'date', 'before_or_equal:today'],
            'type' => ['sometimes', 'required', 'string', 'in:income,expense,savings,bank_deposit'],
            'category_id' => ['sometimes', 'required', 'integer', 'exists:financial_categories,id'],
            'amount' => ['sometimes', 'required', 'numeric', 'min:0.01', 'max:9999999.99'],
            'description' => ['nullable', 'string', 'max:1000'],
            'status' => ['sometimes', 'required', 'string', 'in:completed,pending,cancelled'],
            'reference_number' => ['nullable', 'string', 'max:50'],
        ];
    }
}
