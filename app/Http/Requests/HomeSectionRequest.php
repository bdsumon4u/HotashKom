<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class HomeSectionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
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

        if ($this->get('content')) {
            $this->merge([
                'type' => 'content',
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            'title' => 'required',
            'type' => 'required',
            'items' => 'nullable|array',
            'categories' => 'nullable|array',
        ];

        // Only require rows/cols for product sections (not content or banner)
        if (! $this->get('content') && ! $this->get('banner') && $this->get('type') != 'content' && $this->get('type') != 'banner') {
            $rules += [
                'data.rows' => 'required|integer',
                'data.cols' => 'required|integer',
                'data.source' => 'nullable',
            ];
        }

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

        if ($this->get('content') || $this->get('type') == 'content') {
            $rules += [
                'data.page_id' => 'required|exists:pages,id',
            ];
        }

        return $rules;
    }
}
