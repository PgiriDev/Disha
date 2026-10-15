<?php

namespace App\Http\Requests\Admin;

use Illuminate\Validation\Rule;

class UpdateAirportRequest extends StoreAirportRequest
{
    public function rules(): array
    {
        $rules = parent::rules();

        $airport = $this->route('airport');

        $rules['iata_code'] = ['required', 'string', 'size:3', 'alpha', Rule::unique('airports', 'iata_code')->ignore($airport)];
        $rules['icao_code'] = ['required', 'string', 'size:4', 'alpha', Rule::unique('airports', 'icao_code')->ignore($airport)];

        return $rules;
    }
}
