<?php

namespace App\Http\Requests;

use App\Models\LandingPagePro;
use App\Services\LandingPageProTemplateRegistry;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLandingPageProRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $settings = $this->normalizeSectionSettings($this->input('section_settings', []));

        $this->merge([
            'is_published' => $this->boolean('is_published'),
            'section_settings' => $settings,
        ]);
    }

    public function rules(): array
    {
        $templates = app(LandingPageProTemplateRegistry::class)->options();

        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'regex:/^[a-zA-Z0-9-]+$/', 'unique:landing_page_pros,slug'],
            'template_key' => ['required', 'in:'.implode(',', array_keys($templates))],
            'is_published' => ['required', 'boolean'],
            'published_at' => ['nullable', 'date'],
            'seo.title' => ['nullable', 'string', 'max:255'],
            'seo.description' => ['nullable', 'string', 'max:500'],
            'section_settings' => ['nullable', 'array'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', Rule::exists('products', 'id')->whereNull('parent_id'), 'distinct'],
            'items.*.free_delivery' => ['nullable', 'boolean'],
            'items.*.is_active' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'slug.regex' => 'The link field may only contain letters, numbers, and hyphens. No spaces or special characters are allowed.',
            'items.required' => 'Please select at least one product or variation for this landing page.',
            'items.*.product_id.distinct' => 'Each product/variation can only be selected once.',
        ];
    }

    private function normalizeSectionSettings(array $raw): array
    {
        $defaults = LandingPagePro::defaultSectionSettings();
        $sectionKeys = array_keys(LandingPagePro::sectionLabels());
        $ctaKeys = LandingPagePro::ctaSectionKeys();
        $allKeys = array_merge($sectionKeys, $ctaKeys);
        $reorderableSectionKeys = array_keys(LandingPagePro::reorderableSectionLabels());
        $normalized = [];

        foreach ($allKeys as $section) {
            $config = $defaults[$section] ?? ['enabled' => true];
            $current = $raw[$section] ?? [];
            $normalized[$section] = is_array($current) ? $current : [];
            $normalized[$section]['enabled'] = (bool) ($current['enabled'] ?? ($config['enabled'] ?? false));
        }

        $normalizedOrder = collect(data_get($raw, 'section_order', []))
            ->map(fn ($key): string => (string) $key)
            ->filter(fn (string $key): bool => in_array($key, $reorderableSectionKeys, true))
            ->unique()
            ->values();

        $missingKeys = collect($reorderableSectionKeys)
            ->reject(fn (string $key): bool => $normalizedOrder->contains($key))
            ->values();

        $normalized['section_order'] = $normalizedOrder
            ->concat($missingKeys)
            ->values()
            ->all();

        return array_replace_recursive($defaults, $normalized);
    }
}
