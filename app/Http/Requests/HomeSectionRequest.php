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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required',
            'type' => 'required',
            'items' => 'nullable|array',
            'categories' => 'nullable|array',
            'data.rows' => 'required|integer',
            'data.cols' => 'required|integer',
            'data.source' => 'nullable',
        ];
    }
}
