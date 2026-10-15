<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTerminalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'code' => $this->filled('code') ? strtoupper(trim((string) $this->input('code'))) : $this->input('code'),
        ]);
    }

    public function rules(): array
    {
        return [
            'airport_id' => ['required', 'integer', 'exists:airports,id'],
            'name' => ['required', 'string', 'max:255'],
            'code' => [
                'required', 'string', 'max:10',
                Rule::unique('terminals', 'code')->where(fn ($query) => $query->where('airport_id', $this->input('airport_id'))),
            ],
            'passenger_capacity' => ['nullable', 'integer', 'min:0', 'max:1000000'],
        ];
    }

    public function messages(): array
    {
        return [
            'code.unique' => 'This terminal code is already used at the selected airport.',
        ];
    }
}
