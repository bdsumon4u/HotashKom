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

        $hasFixedTotalPricing = $this->input('section_settings.order_form.quantity_pricing_type') === 'fixed_total';

        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'regex:/^[a-zA-Z0-9-]+$/', 'unique:landing_page_pros,slug'],
            'template_key' => ['required', 'in:'.implode(',', array_keys($templates))],
            'is_published' => ['required', 'boolean'],
            'published_at' => ['nullable', 'date'],
            'seo.title' => ['nullable', 'string', 'max:255'],
            'seo.description' => ['nullable', 'string', 'max:500'],
            'custom_css' => ['nullable', 'string'],
            'section_settings' => ['nullable', 'array'],
            'section_settings.*' => ['nullable'],
            'section_settings.order_form.quantity_pricing_type' => ['required', Rule::in(['percentage', 'fixed_total'])],
            'section_settings.order_form.quantity_discounts' => ['nullable', 'array', 'max:10'],
            'section_settings.order_form.quantity_discounts.*' => ['required', 'array:min_quantity,percentage,total_price'],
            'section_settings.order_form.quantity_discounts.*.min_quantity' => ['required', 'integer', 'min:1', 'max:100', 'distinct'],
            'section_settings.order_form.quantity_discounts.*.percentage' => [Rule::excludeIf($hasFixedTotalPricing), 'required', 'integer', 'min:1', 'max:100'],
            'section_settings.order_form.quantity_discounts.*.total_price' => [Rule::excludeIf(! $hasFixedTotalPricing), 'required', 'integer', 'min:1', 'max:10000000'],
            'section_settings.features.rows' => ['nullable', 'array', 'max:100'],
            'section_settings.features.rows.*' => ['required', 'array:text'],
            'section_settings.features.rows.*.text' => ['required', 'string', 'max:1000'],
            'section_settings.size_guide.rows' => ['nullable', 'array', 'max:100'],
            'section_settings.size_guide.rows.*' => ['required', 'array:size,waist,length'],
            'section_settings.size_guide.rows.*.size' => ['required', 'string', 'max:100'],
            'section_settings.size_guide.rows.*.waist' => ['required', 'string', 'max:100'],
            'section_settings.size_guide.rows.*.length' => ['required', 'string', 'max:100'],
            'section_settings.faq.rows' => ['nullable', 'array', 'max:100'],
            'section_settings.faq.rows.*' => ['required', 'array:q,a'],
            'section_settings.faq.rows.*.q' => ['required', 'string', 'max:500'],
            'section_settings.faq.rows.*.a' => ['required', 'string', 'max:10000'],
            'section_settings.reviews.rows' => ['nullable', 'array', 'max:100'],
            'section_settings.reviews.rows.*' => ['required', 'array:name,text'],
            'section_settings.reviews.rows.*.name' => ['required', 'string', 'max:255'],
            'section_settings.reviews.rows.*.text' => ['required', 'string', 'max:10000'],
            'section_settings.size_guide.headers' => ['nullable', 'array:size,waist,length'],
            'section_settings.size_guide.headers.*' => ['required', 'string', 'max:100'],
            'section_settings.phone' => ['nullable', 'string', 'max:50'],
            'section_settings.whatsapp' => ['nullable', 'string', 'max:50'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', Rule::exists('products', 'id')->whereNull('parent_id'), 'distinct'],
            'items.*.free_delivery' => ['nullable', 'boolean'],
            'items.*.is_active' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'section_settings.order_form.quantity_discounts.*.min_quantity.distinct' => 'Use each quantity only once in your pricing tiers.',
            'section_settings.order_form.quantity_discounts.*.min_quantity.min' => 'The minimum quantity must be at least 1 piece.',
            'section_settings.order_form.quantity_discounts.*.percentage.required' => 'Enter a discount percentage for each tier.',
            'section_settings.order_form.quantity_discounts.*.percentage.max' => 'The discount percentage cannot exceed 100%.',
            'section_settings.order_form.quantity_discounts.*.total_price.required' => 'Enter the total price in taka for each quantity.',
            'section_settings.order_form.quantity_discounts.*.total_price.min' => 'The total offer price must be at least 1 taka.',
            'section_settings.features.rows.*.text.required' => 'Enter the feature for features row #:position, or remove that row.',
            'section_settings.size_guide.rows.*.size.required' => 'Enter the size for size guide row #:position, or remove that row.',
            'section_settings.size_guide.rows.*.waist.required' => 'Enter the waist measurement for size guide row #:position, or remove that row.',
            'section_settings.size_guide.rows.*.length.required' => 'Enter the length measurement for size guide row #:position, or remove that row.',
            'section_settings.faq.rows.*.q.required' => 'Enter the question for faq row #:position, or remove that row.',
            'section_settings.faq.rows.*.a.required' => 'Enter the answer for faq row #:position, or remove that row.',
            'section_settings.reviews.rows.*.name.required' => 'Enter the customer name for reviews row #:position, or remove that row.',
            'section_settings.reviews.rows.*.text.required' => 'Enter the review for reviews row #:position, or remove that row.',
            'section_settings.size_guide.headers.*.required' => 'Enter a label for each size guide column heading.',
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

        foreach (['features', 'size_guide', 'faq', 'reviews'] as $section) {
            if (data_get($raw, "$section.rows_present")) {
                $rows = data_get($raw, "$section.rows", []);
                $normalized[$section]['rows'] = is_array($rows)
                    ? array_values(array_filter($rows, fn ($row): bool => ! is_array($row) || collect($row)->contains(fn ($value): bool => filled($value))))
                    : $rows;
            }
            unset($normalized[$section]['rows_present']);
        }

        $normalized['order_form']['quantity_pricing_type'] = data_get($raw, 'order_form.quantity_pricing_type', 'percentage');
        $normalized['order_form']['quantity_discounts'] = data_get($raw, 'order_form.quantity_discounts', []);

        $normalized['phone'] = filled(data_get($raw, 'phone')) ? trim((string) data_get($raw, 'phone')) : null;
        $normalized['whatsapp'] = filled(data_get($raw, 'whatsapp')) ? trim((string) data_get($raw, 'whatsapp')) : null;

        return array_replace_recursive($defaults, $normalized);
    }
}
