<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreGateRequest extends FormRequest
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
            'terminal_id' => ['required', 'integer', 'exists:terminals,id'],
            'code' => [
                'required', 'string', 'max:10',
                Rule::unique('gates', 'code')->where(fn ($query) => $query->where('terminal_id', $this->input('terminal_id'))),
            ],
            'status' => ['required', 'in:available,occupied,closed'],
        ];
    }

    public function messages(): array
    {
        return [
            'code.unique' => 'This gate code is already used in the selected terminal.',
        ];
    }
}
