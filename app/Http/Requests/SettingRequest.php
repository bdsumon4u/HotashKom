<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SettingRequest extends FormRequest
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
        if ($this->isMethod('GET')) {
            return [];
        }

        if ($this->get('tab') == 'company') {
            return [
                'logo' => ['sometimes', 'array'],
                'logo.*' => ['nullable', 'image'],
                'company' => ['required', 'array'],
                'company.name' => ['required'],
                'company.contact_name' => ['required'],
                'company.email' => ['required'],
                'company.phone' => ['required'],
                'company.whatsapp' => ['nullable'],
                'company.tagline' => ['required'],
                'company.address' => ['required'],
                'company.office_time' => ['required'],
                'company.messenger' => ['nullable'],
                'company.gmap_ecode' => ['nullable'],
                'company.dev_name' => ['nullable'],
                'company.dev_link' => ['nullable'],
                'call_for_order' => ['required'],
                'social' => ['required', 'array'],
            ];
        }

        if ($this->get('tab') == 'delivery') {
            return [
                'delivery_charge.inside_dhaka' => 'sometimes|integer',
                'delivery_charge.outside_dhaka' => 'sometimes|integer',
                'delivery_text' => 'sometimes',
                'free_delivery' => 'sometimes',
                'default_area' => 'required|array',
                'show_option' => 'required|array',
            ];
        }

        if ($this->get('tab') == 'analytics') {
            return [
                'gtm_id' => 'sometimes',
                'pixel_ids' => 'sometimes',
                'scripts' => 'sometimes',
            ];
        }

        if ($this->get('tab') == 'courier') {
            return [
                'Pathao' => 'required|array',
                'SteadFast' => 'required|array',
            ];
        }

        if ($this->get('tab') == 'sms') {
            return [
                'ElitBuzz' => 'required|array',
                'BDWebs' => 'required|array',
                'SMSTemplates' => 'required|array',
                'show_option' => 'required|array',
            ];
        }

        if ($this->get('tab') == 'fraud') {
            return [
                'fraud' => 'required|array',
            ];
        }

        if ($this->get('tab') == 'color') {
            $rules = [];
            foreach (['topbar', 'header', 'search', 'navbar', 'category_menu', 'section', 'badge', 'footer', 'primary', 'add_to_cart', 'order_now'] as $key) {
                $rules['color.'.$key] = 'required|array';
                foreach (['background_color', 'background_hover', 'text_color', 'text_hover'] as $color) {
                    $rules['color.'.$key.'.'.$color] = ['required', 'regex:/^#([a-f0-9]{6}|[a-f0-9]{3})$/i'];
                }
            }

            return $rules;
        }

        if (in_array($this->get('tab'), ['categories', 'brands'])) {
            return [
                'show_option' => 'required|array',
            ];
        }

        return [
            'products_page' => 'required|array',
            'related_products' => 'required|array',
            'scroll_text' => 'nullable',
            'show_option' => 'required|array',
            'discount_text' => 'required',
            'services' => 'required|array',
        ];
    }
}
