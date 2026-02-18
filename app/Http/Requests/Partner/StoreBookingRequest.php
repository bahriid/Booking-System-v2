<?php

declare(strict_types=1);

namespace App\Http\Requests\Partner;

use App\Enums\PaxType;
use App\Models\TourDeparture;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validates booking creation request from partner portal.
 */
final class StoreBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->isPartner();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'tour_departure_id' => [
                'required',
                'integer',
                Rule::exists('tour_departures', 'id')->where(function ($query) {
                    $query->where('status', 'open');
                }),
            ],
            'passengers' => ['required', 'array', 'min:1'],
            'passengers.*.first_name' => ['required', 'string', 'max:100'],
            'passengers.*.last_name' => ['required', 'string', 'max:100'],
            'passengers.*.phone' => ['nullable', 'string', 'max:50'],
            'passengers.*.pickup_point_id' => ['required', 'exists:pickup_points,id'],
            'passengers.*.pax_type' => ['required', Rule::enum(PaxType::class)],
            'passengers.*.allergies' => ['nullable', 'string', 'max:255'],
            'passengers.*.notes' => ['nullable', 'string', 'max:500'],
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
            'tour_departure_id.required' => 'Please select a tour departure.',
            'tour_departure_id.exists' => 'The selected departure is not available.',
            'passengers.required' => 'At least one passenger is required.',
            'passengers.min' => 'At least one passenger is required.',
            'passengers.*.first_name.required' => 'First name is required for all passengers.',
            'passengers.*.last_name.required' => 'Last name is required for all passengers.',
            'passengers.*.pickup_point_id.required' => 'Pickup point is required for all passengers.',
            'passengers.*.pax_type.required' => 'Passenger type is required.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $departure = TourDeparture::find($this->input('tour_departure_id'));

            if (!$departure) {
                return;
            }

            // Check if past cut-off
            if ($departure->isPastCutoff()) {
                $validator->errors()->add(
                    'tour_departure_id',
                    "Booking is closed. Cut-off time is {$departure->tour?->cutoff_hours} hours before departure."
                );
            }
        });
    }
}
