<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

final class BulkCloseDeparturesRequest extends FormRequest
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
            'tour_id' => ['nullable', 'exists:tours,id'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'times' => ['nullable', 'array'],
            'times.*' => ['date_format:H:i'],
            'reason' => ['nullable', 'string', 'max:500'],
            'notify_partners' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * Get the validated data with defaults.
     */
    public function validatedWithDefaults(): array
    {
        $validated = $this->validated();
        $validated['notify_partners'] = $this->boolean('notify_partners', true);

        return $validated;
    }
}
