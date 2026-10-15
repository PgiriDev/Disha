<?php

namespace App\Http\Requests\Admin;

use Illuminate\Validation\Rule;

class UpdateTerminalRequest extends StoreTerminalRequest
{
    public function rules(): array
    {
        $rules = parent::rules();

        $terminal = $this->route('terminal');

        $rules['code'] = [
            'required', 'string', 'max:10',
            Rule::unique('terminals', 'code')
                ->where(fn ($query) => $query->where('airport_id', $this->input('airport_id')))
                ->ignore($terminal),
        ];

        return $rules;
    }
}
