<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class StoreLeadRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $hidePrefix = setting('show_option')->hide_phone_prefix ?? false;
        $phoneRules = $hidePrefix
            ? ['required', 'regex:/^\+8801\d{9}$/']
            : ['required', 'regex:/^1\d{9}$/'];

        return [
            'name' => ['required', 'string', 'max:120'],
            'shop_name' => ['nullable', 'string', 'max:150'],
            'district' => ['required', 'string', 'max:120'],
            'email' => ['nullable', 'email', 'max:150'],
            'phone' => $phoneRules,
            'message' => ['nullable', 'string', 'max:1000'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $phone = preg_replace('/[^0-9+]/', '', (string) $this->input('phone'));
        $hidePrefix = setting('show_option')->hide_phone_prefix ?? false;

        if (! $hidePrefix) {
            if (Str::startsWith($phone, '01')) {
                $phone = Str::after($phone, '0');
            }
        } elseif (Str::startsWith($phone, '01')) {
            $phone = '+88'.$phone;
        }

        $this->merge([
            'phone' => $phone,
        ]);
    }
}
