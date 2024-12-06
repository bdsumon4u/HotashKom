<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

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

            $images = Arr::get($this->get('data'), 'columns.image', []);
            if ($this->base_image_src) {
                array_push($images, str_replace(asset(''), '', $this->base_image_src));
            }

            $this->merge([
                'data' => array_merge($this->get('data'), [
                    'columns' => array_merge(Arr::get($this->get('data'), 'columns', []), [
                        'image' => $images,
                    ]),
                ]),
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
                'data.columns.image.*' => 'required|string',
                'data.columns.animation.*' => 'required|string',
                'data.columns.link.*' => 'nullable|string',
                'data.columns.width.*' => 'required|numeric',
                'data.columns.categories.*' => 'nullable|array',
            ];
        }

        return $rules;
    }
}
