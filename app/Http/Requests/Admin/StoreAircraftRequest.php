<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreAircraftRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'registration_number' => $this->filled('registration_number') ? strtoupper(trim((string) $this->input('registration_number'))) : $this->input('registration_number'),
            'business_capacity' => $this->input('business_capacity', 0) ?: 0,
            'economy_capacity' => $this->input('economy_capacity', 0) ?: 0,
        ]);
    }

    public function rules(): array
    {
        return [
            'airline_id' => ['required', 'integer', 'exists:airlines,id'],
            'registration_number' => ['required', 'string', 'max:10', 'unique:aircraft,registration_number'],
            'manufacturer' => ['required', 'string', 'max:255'],
            'model' => ['required', 'string', 'max:255'],
            'seat_capacity' => ['required', 'integer', 'min:1', 'max:1000'],
            'business_capacity' => ['nullable', 'integer', 'min:0'],
            'economy_capacity' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'in:active,maintenance,retired'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $seat = (int) $this->input('seat_capacity');
                $business = (int) $this->input('business_capacity', 0);
                $economy = (int) $this->input('economy_capacity', 0);

                if ($seat > 0 && ($business + $economy) > $seat) {
                    $validator->errors()->add(
                        'business_capacity',
                        'The combined business and economy capacity cannot exceed the total seat capacity.'
                    );
                }
            },
        ];
    }
}
