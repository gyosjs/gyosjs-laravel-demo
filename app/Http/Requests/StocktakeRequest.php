<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class StocktakeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'counts' => ['required', 'array', 'min:1'],
            'counts.*' => ['required', 'integer', 'min:0', 'max:1000000'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $workspace = $this->attributes->get('demoWorkspace');
            $counts = $this->input('counts', []);

            if (! $workspace || ! is_array($counts)) {
                return;
            }

            $productCount = $workspace->products()->whereKey(array_keys($counts))->count();

            if ($productCount !== count($counts)) {
                $validator->errors()->add('counts', 'One or more products do not belong to this workspace.');
            }
        });
    }
}
