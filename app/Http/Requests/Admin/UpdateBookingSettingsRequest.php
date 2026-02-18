<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateBookingSettingsRequest extends FormRequest
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
            'cutoff_hours' => ['required', 'integer', 'min:1', 'max:168'],
            'overbooking_expiry_hours' => ['required', 'integer', 'min:1', 'max:24'],
            'free_cancellation_hours' => ['required', 'integer', 'min:1', 'max:168'],
            'late_cancellation_penalty' => ['required', 'integer', 'min:0', 'max:100'],
            'overbooking_enabled' => ['sometimes', 'boolean'],
        ];
    }
}
