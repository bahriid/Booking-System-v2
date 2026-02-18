<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validates credit/refund recording request.
 */
final class StoreCreditRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'partner_id' => ['required', 'exists:partners,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'reason' => ['required', 'string', 'in:bad_weather,tour_cancelled,other'],
            'booking_code' => ['nullable', 'string', 'max:50'],
            'date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'amount.min' => 'Credit amount must be greater than zero.',
            'reason.in' => 'Please select a valid reason for the credit.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Combine reason and notes for the payment description
        if ($this->reason && $this->reason !== 'other') {
            $reasonLabels = [
                'bad_weather' => 'Bad weather cancellation',
                'tour_cancelled' => 'Tour cancelled by operator',
            ];
            $this->merge([
                'notes' => ($reasonLabels[$this->reason] ?? $this->reason) .
                    ($this->booking_code ? " - {$this->booking_code}" : '') .
                    ($this->notes ? " - {$this->notes}" : ''),
            ]);
        }
    }
}
