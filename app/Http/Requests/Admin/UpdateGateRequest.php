<?php

namespace App\Http\Requests\Admin;

use Illuminate\Validation\Rule;

class UpdateGateRequest extends StoreGateRequest
{
    public function rules(): array
    {
        $rules = parent::rules();

        $gate = $this->route('gate');

        $rules['code'] = [
            'required', 'string', 'max:10',
            Rule::unique('gates', 'code')
                ->where(fn ($query) => $query->where('terminal_id', $this->input('terminal_id')))
                ->ignore($gate),
        ];

        return $rules;
    }
}
