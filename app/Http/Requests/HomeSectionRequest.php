<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HomeSectionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    protected function prepareForValidation()
    {
        if ($this->get('banner')) {
            $this->merge([
                'title' => 'Banner',
                'type' => 'banner',
                'data' => array_merge([
                    'rows' => 1,
                    'cols' => 1,
                    'source' => null,
                ], $this->get('data', [])),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'title' => 'required',
            'type' => 'required',
            'items' => 'nullable|array',
            'categories' => 'nullable|array',
            'data.rows' => 'required|integer',
            'data.cols' => 'required|integer',
            'data.source' => 'nullable',
        ];

        if ($this->get('banner')) {
            $rules += [
                'data.columns' => 'required|array',
                'data.columns.image.*' => 'required|url',
                'data.columns.animation.*' => 'required|string',
                'data.columns.link.*' => 'nullable|string',
                'data.columns.width.*' => 'required|numeric',
                'data.columns.categories.*' => 'nullable|array',
            ];
        }

        return $rules;
    }
}
