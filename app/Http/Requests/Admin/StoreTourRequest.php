<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Validates tour creation request from admin panel.
 */
final class StoreTourRequest extends FormRequest
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
            'code' => ['required', 'string', 'max:20', 'unique:tours,code', 'regex:/^[A-Z0-9]+$/'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'seasonality_start' => ['required', 'date'],
            'seasonality_end' => ['required', 'date'],
            'cutoff_hours' => ['required', 'integer', 'min:1', 'max:168'],
            'default_capacity' => ['required', 'integer', 'min:1', 'max:500'],
            'is_active' => ['boolean'],
        ];
    }

    /**
     * Get custom error messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'code.regex' => 'The tour code must contain only uppercase letters and numbers.',
            'code.unique' => 'This tour code is already in use.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $data = [
            'code' => strtoupper($this->code ?? ''),
            'is_active' => $this->boolean('is_active'),
        ];

        if ($this->seasonality_start) {
            $data['seasonality_start'] = Carbon::createFromFormat('d/m/Y', $this->seasonality_start)->format('Y-m-d');
        }

        if ($this->seasonality_end) {
            $data['seasonality_end'] = Carbon::createFromFormat('d/m/Y', $this->seasonality_end)->format('Y-m-d');
        }

        $this->merge($data);
    }
}
