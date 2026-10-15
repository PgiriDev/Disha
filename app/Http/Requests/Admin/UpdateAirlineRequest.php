<?php

namespace App\Http\Requests\Admin;

use Illuminate\Validation\Rule;

class UpdateAirlineRequest extends StoreAirlineRequest
{
    public function rules(): array
    {
        $rules = parent::rules();

        $airline = $this->route('airline');

        $rules['iata_code'] = ['required', 'string', 'size:2', 'alpha', Rule::unique('airlines', 'iata_code')->ignore($airline)];
        $rules['icao_code'] = ['required', 'string', 'size:3', 'alpha', Rule::unique('airlines', 'icao_code')->ignore($airline)];

        return $rules;
    }
}
