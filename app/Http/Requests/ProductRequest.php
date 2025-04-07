<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation(): void
    {
        $sku = $this->get('sku');
        $this->merge(['sku' => strtoupper((string) $sku)]);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            'name' => 'required|max:255',
            'slug' => 'required|max:255|unique:products',
            'description' => 'required',
            'categories' => 'required|array',
            'brand' => 'nullable|integer',
            'price' => 'required|integer',
            'selling_price' => 'required|integer',
            'suggested_price' => 'nullable',
            'wholesale.quantity' => 'sometimes|array',
            'wholesale.price' => 'sometimes|array',
            'wholesale.quantity.*' => 'required|integer|gt:1',
            'wholesale.price.*' => 'required|integer|min:1',
            'sku' => 'required|unique:products',
            'should_track' => 'sometimes|integer',
            'stock_count' => 'nullable|required_if:should_track,1|integer',
            'is_active' => 'sometimes|boolean',
            'base_image' => 'required|integer',
            'additional_images' => 'sometimes|array',
            'desc_img' => 'required|boolean',
            'desc_img_pos' => 'required_if:desc_img,1',
            'shipping_inside' => 'nullable|integer',
            'shipping_outside' => 'nullable|integer',
            'delivery_text' => 'nullable',
        ];

        if (! $this->isMethod('POST')) {
            $rules['slug'] = 'required|max:255|unique:products,slug,'.$this->route('product')->id;
            $rules['sku'] = 'required|unique:products,sku,'.$this->route('product')->id;
        }

        return $rules;
    }

    public function all($keys = null)
    {
        $data = parent::all() + [
            'is_active' => 0,
        ];

        $data['brand_id'] = $data['brand'];
        $data['stock_count'] = intval($data['stock_count']);

        return $data;
    }
}
