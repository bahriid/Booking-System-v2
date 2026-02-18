<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Request validation for bulk marking bookings as paid.
 */
final class BulkMarkBookingsAsPaidRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'booking_ids' => ['required', 'array', 'min:1'],
            'booking_ids.*' => ['required', 'integer', 'exists:bookings,id'],
            'method' => ['required', 'string', 'in:bank_transfer,cash,credit_card,check'],
            'paid_at' => ['required', 'date'],
            'reference' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Get custom messages for validation errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'booking_ids.required' => 'Please select at least one booking to mark as paid.',
            'booking_ids.min' => 'Please select at least one booking to mark as paid.',
        ];
    }
}
