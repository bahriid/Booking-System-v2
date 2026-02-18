<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Enums\Season;
use App\Models\Tour;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validates bulk tour departure creation request.
 */
final class BulkCreateDeparturesRequest extends FormRequest
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
        $maxCapacity = 500;
        if ($this->tour_id) {
            $tour = Tour::find($this->tour_id);
            if ($tour) {
                $maxCapacity = $tour->default_capacity;
            }
        }

        return [
            'tour_id' => ['required', 'exists:tours,id'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'days' => ['required', 'array', 'min:1'],
            'days.*' => ['integer', 'min:0', 'max:6'],
            'time' => ['required', 'date_format:H:i'],
            'capacity' => ['required', 'integer', 'min:1', 'max:'.$maxCapacity],
            'season' => ['required', Rule::enum(Season::class)],
            'notes' => ['nullable', 'string', 'max:500'],
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
            'days.required' => 'Please select at least one day of the week.',
            'days.min' => 'Please select at least one day of the week.',
        ];
    }
}
