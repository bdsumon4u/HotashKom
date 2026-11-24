<?php

namespace App\Http\Requests;

use App\Models\LandingPagePro;
use App\Services\LandingPageProTemplateRegistry;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLandingPageProRequest extends FormRequest
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
        $landingPagePro = $this->route('landing_page_pro');
        $templates = app(LandingPageProTemplateRegistry::class)->options();

        $hasConditionalPricing = $this->boolean('section_settings.order_form.conditional_pricing_enabled');
        $campaignProductIds = collect($this->input('items', []))
            ->filter(fn ($item): bool => filter_var(data_get($item, 'is_active', true), FILTER_VALIDATE_BOOL))
            ->pluck('product_id')->filter()->all();

        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'regex:/^[a-zA-Z0-9-]+$/', 'unique:landing_page_pros,slug,'.$landingPagePro->id],
            'template_key' => ['required', 'in:'.implode(',', array_keys($templates))],
            'is_published' => ['required', 'boolean'],
            'published_at' => ['nullable', 'date'],
            'seo.title' => ['nullable', 'string', 'max:255'],
            'seo.description' => ['nullable', 'string', 'max:500'],
            'custom_css' => ['nullable', 'string'],
            'section_settings' => ['nullable', 'array'],
            'section_settings.*' => ['nullable'],
            'section_settings.order_form.conditional_pricing_enabled' => ['sometimes', 'boolean'],
            'section_settings.order_form.conditional_rules' => [Rule::requiredIf($hasConditionalPricing), 'array', $hasConditionalPricing ? 'min:1' : 'min:0', 'max:30'],
            'section_settings.order_form.conditional_rules.*' => ['required', 'array:name,total_price,requirements,scope,min_quantity'],
            'section_settings.order_form.conditional_rules.*.scope' => ['sometimes', Rule::in(['products', 'any'])],
            'section_settings.order_form.conditional_rules.*.min_quantity' => ['exclude_unless:section_settings.order_form.conditional_rules.*.scope,any', 'required', 'integer', 'min:1', 'max:100'],
            'section_settings.order_form.conditional_rules.*.name' => ['nullable', 'string', 'max:100'],
            'section_settings.order_form.conditional_rules.*.total_price' => ['required', 'integer', 'min:1', 'max:10000000'],
            'section_settings.order_form.conditional_rules.*.requirements' => [
                'exclude_if:section_settings.order_form.conditional_rules.*.scope,any', 'required', 'array', 'min:1', 'max:10',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if (is_array($value) && collect($value)->pluck('product_id')->filter(fn ($id): bool => is_scalar($id))->map(fn ($id): string => (string) $id)->duplicates()->isNotEmpty()) {
                        $fail('Select each product only once within an offer. Combine its quantity into one condition.');
                    }
                },
            ],
            'section_settings.order_form.conditional_rules.*.requirements.*' => ['required', 'array:product_id,min_quantity'],
            'section_settings.order_form.conditional_rules.*.requirements.*.product_id' => [
                'required', 'integer',
                ...($hasConditionalPricing ? [Rule::in($campaignProductIds)] : []),
            ],
            'section_settings.order_form.conditional_rules.*.requirements.*.min_quantity' => ['required', 'integer', 'min:1', 'max:100'],
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
            'section_settings.features.rows.*.text.required' => 'Enter the feature for features row #:position, or remove that row.',
            'section_settings.size_guide.rows.*.size.required' => 'Enter the size for size guide row #:position, or remove that row.',
            'section_settings.size_guide.rows.*.waist.required' => 'Enter the waist measurement for size guide row #:position, or remove that row.',
            'section_settings.size_guide.rows.*.length.required' => 'Enter the length measurement for size guide row #:position, or remove that row.',
            'section_settings.faq.rows.*.q.required' => 'Enter the question for faq row #:position, or remove that row.',
            'section_settings.faq.rows.*.a.required' => 'Enter the answer for faq row #:position, or remove that row.',
            'section_settings.reviews.rows.*.name.required' => 'Enter the customer name for reviews row #:position, or remove that row.',
            'section_settings.reviews.rows.*.text.required' => 'Enter the review for reviews row #:position, or remove that row.',
            'section_settings.size_guide.headers.*.required' => 'Enter a label for each size guide column heading.',
            'section_settings.order_form.conditional_rules.required' => 'Add at least one offer when conditional combo pricing is enabled.',
            'section_settings.order_form.conditional_rules.min' => 'Add at least one offer when conditional combo pricing is enabled.',
            'section_settings.order_form.conditional_rules.*.total_price.required' => 'Enter a total price for each conditional offer.',
            'section_settings.order_form.conditional_rules.*.requirements.*.product_id.required' => 'Choose a product for every offer condition.',
            'section_settings.order_form.conditional_rules.*.requirements.*.product_id.in' => 'Every offer product must be selected and shown on this landing page. Update its conditions or disable conditional pricing.',
            'section_settings.order_form.conditional_rules.*.requirements.*.min_quantity.required' => 'Enter the minimum pieces for every offer condition.',
            'section_settings.order_form.conditional_rules.*.min_quantity.required' => 'Enter the minimum total pieces for each Any products offer.',
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

        unset($normalized['order_form']['quantity_pricing_type'], $normalized['order_form']['quantity_discounts']);

        $normalized['order_form']['conditional_pricing_enabled'] = filter_var(data_get($raw, 'order_form.conditional_pricing_enabled', false), FILTER_VALIDATE_BOOL);
        $normalized['order_form']['conditional_rules'] = data_get($raw, 'order_form.conditional_rules', []);

        if (is_array($normalized['order_form']['conditional_rules'])) {
            $normalized['order_form']['conditional_rules'] = array_map(function (mixed $rule): mixed {
                if (! is_array($rule)) {
                    return $rule;
                }
                if (($rule['scope'] ?? 'products') === 'any') {
                    unset($rule['requirements']);
                } else {
                    unset($rule['min_quantity']);
                }

                return $rule;
            }, $normalized['order_form']['conditional_rules']);
        }

        $normalized['phone'] = filled(data_get($raw, 'phone')) ? trim((string) data_get($raw, 'phone')) : null;
        $normalized['whatsapp'] = filled(data_get($raw, 'whatsapp')) ? trim((string) data_get($raw, 'whatsapp')) : null;

        return array_replace_recursive($defaults, $normalized);
    }
}
