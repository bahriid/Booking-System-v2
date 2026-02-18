<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpdateGeneralSettingsRequest extends FormRequest
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
            'company_name' => ['required', 'string', 'max:255'],
            'company_email' => ['required', 'email', 'max:255'],
            'company_phone' => ['nullable', 'string', 'max:50'],
            'company_address' => ['nullable', 'string', 'max:1000'],
            'timezone' => ['required', 'string', Rule::in(array_keys(\App\Models\Setting::getTimezones()))],
            'currency' => ['required', 'string', Rule::in(array_keys(\App\Models\Setting::getCurrencies()))],
            'date_format' => ['required', 'string', Rule::in(array_keys(\App\Models\Setting::getDateFormats()))],
        ];
    }
}
