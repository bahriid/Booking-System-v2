<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Enums\PaxType;
use App\Models\TourDeparture;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validates booking creation request from admin panel (on behalf of partner).
 */
final class StoreBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'partner_id' => [
                'required',
                'integer',
                Rule::exists('partners', 'id')->where('is_active', true),
            ],
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
            'partner_id.required' => __('bookings.validation.partner_required'),
            'partner_id.exists' => __('bookings.validation.partner_invalid'),
            'tour_departure_id.required' => __('bookings.validation.departure_required'),
            'tour_departure_id.exists' => __('bookings.validation.departure_not_available'),
            'passengers.required' => __('bookings.validation.passengers_required'),
            'passengers.min' => __('bookings.validation.passengers_required'),
            'passengers.*.first_name.required' => __('bookings.validation.first_name_required'),
            'passengers.*.last_name.required' => __('bookings.validation.last_name_required'),
            'passengers.*.pickup_point_id.required' => __('bookings.validation.pickup_required'),
            'passengers.*.pax_type.required' => __('bookings.validation.pax_type_required'),
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
                    __('bookings.validation.past_cutoff', ['hours' => $departure->tour?->cutoff_hours ?? 0])
                );
            }
        });
    }
}
