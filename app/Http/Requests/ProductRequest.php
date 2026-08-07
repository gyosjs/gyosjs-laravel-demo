<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $workspace = $this->attributes->get('demoWorkspace');
        $productId = $this->route('product');

        return [
            'name' => ['required', 'string', 'max:120'],
            'sku' => [
                'required',
                'string',
                'max:40',
                Rule::unique('products', 'sku')
                    ->where('workspace_id', $workspace->getKey())
                    ->ignore($productId),
            ],
            'category' => ['required', Rule::in(Product::CATEGORIES)],
            'status' => ['required', Rule::in(Product::STATUSES)],
            'price' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'stock' => ['required', 'integer', 'min:0', 'max:1000000'],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
