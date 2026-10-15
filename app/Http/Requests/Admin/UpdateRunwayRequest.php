<?php

namespace App\Http\Requests\Admin;

use Illuminate\Validation\Rule;

class UpdateRunwayRequest extends StoreRunwayRequest
{
    public function rules(): array
    {
        $rules = parent::rules();

        $runway = $this->route('runway');

        $rules['designation'] = [
            'required', 'string', 'max:10',
            Rule::unique('runways', 'designation')
                ->where(fn ($query) => $query->where('airport_id', $this->input('airport_id')))
                ->ignore($runway),
        ];

        return $rules;
    }
}
