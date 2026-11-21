<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLandingPageProRequest;
use App\Http\Requests\UpdateLandingPageProRequest;
use App\Models\LandingPagePro;
use App\Models\Product;
use App\Services\LandingPageProTemplateRegistry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class LandingPageProController extends Controller
{
    public function __construct(private readonly LandingPageProTemplateRegistry $templateRegistry) {}

    public function index()
    {
        abort_if(request()->user()->is(['salesman', 'uploader']), 403, 'You don\'t have permission.');

        return $this->view([
            'landings' => LandingPagePro::query()->latest()->get(),
        ]);
    }

    public function create()
    {
        abort_if(request()->user()->is(['salesman', 'uploader']), 403, 'You don\'t have permission.');

        return $this->view([
            'landingPagePro' => new LandingPagePro([
                'template_key' => 'template-one',
                'is_published' => false,
                'section_settings' => LandingPagePro::defaultSectionSettings(),
            ]),
            'templates' => $this->templateRegistry->options(),
            'productChoices' => $this->productChoices(),
            'accordionSections' => LandingPagePro::sectionLabels(),
            'sectionOrderLabels' => LandingPagePro::reorderableSectionLabels(),
            'richTextSections' => LandingPagePro::getRichTextSections(),
            'sectionsWithoutSubtitle' => LandingPagePro::getSectionsWithoutSubtitle(),
            'createSectionDefaults' => LandingPagePro::getCreateSectionDefaults(),
        ]);
    }

    public function store(StoreLandingPageProRequest $request): RedirectResponse
    {
        abort_if(request()->user()->is(['salesman', 'uploader']), 403, 'You don\'t have permission.');

        (new LandingPagePro)->getConnection()->transaction(function () use ($request): void {
            $landingPagePro = LandingPagePro::query()->create([
                ...$request->safe()->except(['items']),
                'published_at' => $this->resolvePublishedAt($request->boolean('is_published'), $request->input('published_at')),
            ]);

            $this->syncItems($landingPagePro, $request->input('items', []));
        });

        return to_route('admin.landing-page-pros.index')->withSuccess('Landing Page Pro created successfully.');
    }

    public function show(LandingPagePro $landing_page_pro): RedirectResponse
    {
        abort_if(request()->user()->is(['salesman', 'uploader']), 403, 'You don\'t have permission.');

        return to_route('admin.landing-page-pros.edit', $landing_page_pro);
    }

    public function edit(LandingPagePro $landing_page_pro)
    {
        abort_if(request()->user()->is(['salesman', 'uploader']), 403, 'You don\'t have permission.');

        $landing_page_pro->load('items.product');

        return $this->view([
            'landingPagePro' => $landing_page_pro,
            'templates' => $this->templateRegistry->options(),
            'productChoices' => $this->productChoices(),
            'accordionSections' => LandingPagePro::sectionLabels(),
            'sectionOrderLabels' => LandingPagePro::reorderableSectionLabels(),
            'richTextSections' => LandingPagePro::getRichTextSections(),
            'sectionsWithoutSubtitle' => LandingPagePro::getSectionsWithoutSubtitle(),
            'createSectionDefaults' => LandingPagePro::getCreateSectionDefaults(),
        ]);
    }

    public function update(UpdateLandingPageProRequest $request, LandingPagePro $landing_page_pro): RedirectResponse
    {
        abort_if(request()->user()->is(['salesman', 'uploader']), 403, 'You don\'t have permission.');

        $landing_page_pro->getConnection()->transaction(function () use ($request, $landing_page_pro): void {
            $landing_page_pro->update([
                ...$request->safe()->except(['items']),
                'published_at' => $this->resolvePublishedAt($request->boolean('is_published'), $request->input('published_at')),
            ]);

            $this->syncItems($landing_page_pro, $request->input('items', []));
        });

        return to_route('admin.landing-page-pros.index')->withSuccess('Landing Page Pro updated successfully.');
    }

    public function duplicate(LandingPagePro $landing_page_pro): RedirectResponse
    {
        abort_if(request()->user()->is(['salesman', 'uploader']), 403, 'You don\'t have permission.');

        $duplicate = $landing_page_pro->getConnection()->transaction(function () use ($landing_page_pro): LandingPagePro {
            $duplicate = $landing_page_pro->replicate()->unsetRelations()->fill([
                'title' => Str::substr($landing_page_pro->title, 0, 248).' (Copy)',
                'slug' => Str::substr($landing_page_pro->slug, 0, 223).'-copy-'.Str::lower((string) Str::ulid()),
                'is_published' => false,
                'published_at' => null,
            ]);
            $duplicate->save();

            foreach ($landing_page_pro->items()->get() as $item) {
                $duplicate->items()->save($item->replicate(['landing_page_pro_id']));
            }

            return $duplicate;
        });

        return to_route('admin.landing-page-pros.edit', $duplicate)
            ->withSuccess('Landing page copied as a draft. Update its title and link before publishing.');
    }

    public function updateCustomCss(Request $request, LandingPagePro $landing_page_pro): JsonResponse|RedirectResponse
    {
        abort_if(request()->user()->is(['salesman', 'uploader']), 403, 'You don\'t have permission.');

        $landing_page_pro->update([
            'custom_css' => $request->input('custom_css'),
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Custom CSS updated successfully.',
            ]);
        }

        return back()->withSuccess('Custom CSS updated successfully.');
    }

    public function destroy(LandingPagePro $landing_page_pro): RedirectResponse
    {
        abort_unless(request()->user()->is('admin'), 403, 'You don\'t have permission.');

        $landing_page_pro->delete();

        return back()->withSuccess('Landing Page Pro deleted successfully.');
    }

    private function syncItems(LandingPagePro $landingPagePro, array $items): void
    {
        $landingPagePro->items()->delete();

        $filteredItems = collect($items)
            ->filter(fn ($item): bool => filled(data_get($item, 'product_id')))
            ->values()
            ->map(function ($item, $index): array {
                $payload = [
                    'product_id' => (int) data_get($item, 'product_id'),
                    'is_active' => (bool) data_get($item, 'is_active', true),
                    'sort_order' => $index,
                    'free_delivery' => (bool) data_get($item, 'free_delivery', false),
                ];

                return $payload;
            });

        $landingPagePro->items()->createMany($filteredItems->all());
    }

    private function resolvePublishedAt(bool $isPublished, ?string $publishedAt): ?Carbon
    {
        if (! $isPublished) {
            return null;
        }

        if (filled($publishedAt)) {
            return Carbon::parse($publishedAt);
        }

        return now();
    }

    private function productChoices(): Collection
    {
        return Product::query()
            ->whereNull('parent_id')
            ->with(['variations.options.attribute'])
            ->reorder('name')
            ->get(['id', 'name', 'sku', 'is_active', 'selling_price'])
            ->map(function (Product $product): array {
                $variations = $product->variations->where('is_active', true);
                $prices = $variations->isNotEmpty() ? $variations->pluck('selling_price') : collect([$product->selling_price]);

                return [
                    'id' => $product->id,
                    'label' => $product->name,
                    'sku' => $product->sku,
                    'is_active' => (bool) $product->is_active,
                    'image' => $product->thumbnail->first()?->src,
                    'min_price' => (float) $prices->min(),
                    'max_price' => (float) $prices->max(),
                    'variation_count' => $variations->count(),
                    'has_variations' => $product->variations->isNotEmpty(),
                    'attributes' => $variations->flatMap->options
                        ->groupBy('attribute_id')
                        ->map(fn (Collection $options): array => [
                            'name' => $options->first()->attribute?->name ?? 'Options',
                            'values' => $options->pluck('name')->unique()->values()->all(),
                        ])
                        ->values()
                        ->all(),
                ];
            })
            ->values();
    }
}
