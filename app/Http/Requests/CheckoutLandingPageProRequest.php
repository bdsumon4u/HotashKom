<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutLandingPageProRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'address' => ['required', 'string', 'max:2000'],
            'note' => ['nullable', 'string', 'max:1000'],
            'delivery_area' => ['required', 'string'],
            'items' => ['required', 'array', 'min:1', 'max:100'],
            'items.*.landing_product_id' => ['required', 'integer'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id', 'distinct'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:10'],
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'অন্তত একটি প্রোডাক্ট সিলেক্ট করুন।',
            'items.*.product_id.distinct' => 'একই প্রোডাক্ট ও সাইজ একবার নির্বাচন করে পরিমাণ পরিবর্তন করুন।',
            'items.*.product_id.exists' => 'নির্বাচিত প্রোডাক্টটি পাওয়া যাচ্ছে না। পেজ রিফ্রেশ করে আবার চেষ্টা করুন।',
            'items.*.quantity.max' => 'প্রতি প্রোডাক্টের সর্বোচ্চ পরিমাণ ১০ পিস।',
        ];
    }
}
