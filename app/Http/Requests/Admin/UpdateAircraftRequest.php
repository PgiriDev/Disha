<?php

namespace App\Http\Requests\Admin;

use Illuminate\Validation\Rule;

class UpdateAircraftRequest extends StoreAircraftRequest
{
    public function rules(): array
    {
        $rules = parent::rules();

        $aircraft = $this->route('aircraft');

        $rules['registration_number'] = ['required', 'string', 'max:10', Rule::unique('aircraft', 'registration_number')->ignore($aircraft)];

        return $rules;
    }
}
