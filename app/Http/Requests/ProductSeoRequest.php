<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductSeoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'seo.title' => 'nullable|string|max:255',
            'seo.description' => 'nullable|string|max:500',
            'seo.image' => 'nullable|url|max:500',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'seo.title.max' => 'The SEO title may not be greater than 255 characters.',
            'seo.description.max' => 'The SEO description may not be greater than 500 characters.',
            'seo.image.url' => 'The SEO image must be a valid URL.',
            'seo.image.max' => 'The SEO image URL may not be greater than 500 characters.',
        ];
    }
}
