<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreAirlineRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'iata_code' => $this->filled('iata_code') ? strtoupper(trim((string) $this->input('iata_code'))) : $this->input('iata_code'),
            'icao_code' => $this->filled('icao_code') ? strtoupper(trim((string) $this->input('icao_code'))) : $this->input('icao_code'),
            'is_active' => $this->boolean('is_active'),
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'iata_code' => ['required', 'string', 'size:2', 'alpha', 'unique:airlines,iata_code'],
            'icao_code' => ['required', 'string', 'size:3', 'alpha', 'unique:airlines,icao_code'],
            'country' => ['required', 'string', 'max:255'],
            'callsign' => ['nullable', 'string', 'max:255'],
            'is_active' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'iata_code.size' => 'The IATA code must be exactly 2 letters.',
            'icao_code.size' => 'The ICAO code must be exactly 3 letters.',
        ];
    }
}
