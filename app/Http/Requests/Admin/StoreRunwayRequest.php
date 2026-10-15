<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRunwayRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'designation' => $this->filled('designation') ? strtoupper(trim((string) $this->input('designation'))) : $this->input('designation'),
        ]);
    }

    public function rules(): array
    {
        return [
            'airport_id' => ['required', 'integer', 'exists:airports,id'],
            'designation' => [
                'required', 'string', 'max:10',
                Rule::unique('runways', 'designation')->where(fn ($query) => $query->where('airport_id', $this->input('airport_id'))),
            ],
            'length_m' => ['required', 'integer', 'min:1', 'max:10000'],
            'width_m' => ['nullable', 'integer', 'min:1', 'max:500'],
            'surface' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:active,maintenance,closed'],
        ];
    }

    public function messages(): array
    {
        return [
            'designation.unique' => 'This runway designation already exists at the selected airport.',
        ];
    }
}
