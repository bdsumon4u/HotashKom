<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLandingPageProRequest;
use App\Http\Requests\UpdateLandingPageProRequest;
use App\Models\LandingPagePro;
use App\Models\Product;
use App\Services\LandingPageProTemplateRegistry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

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
        ]);
    }

    public function store(StoreLandingPageProRequest $request): RedirectResponse
    {
        abort_if(request()->user()->is(['salesman', 'uploader']), 403, 'You don\'t have permission.');

        $landingPagePro = LandingPagePro::query()->create([
            ...$request->safe()->except(['items']),
            'published_at' => $this->resolvePublishedAt($request->boolean('is_published'), $request->input('published_at')),
        ]);

        $this->syncItems($landingPagePro, $request->input('items', []));

        return to_route('admin.landing-page-pros.index')->withSuccess('Landing Page Pro created successfully.');
    }

    public function edit(LandingPagePro $landing_page_pro)
    {
        abort_if(request()->user()->is(['salesman', 'uploader']), 403, 'You don\'t have permission.');

        $landing_page_pro->load('items.product');

        return $this->view([
            'landingPagePro' => $landing_page_pro,
            'templates' => $this->templateRegistry->options(),
            'productChoices' => $this->productChoices(),
        ]);
    }

    public function update(UpdateLandingPageProRequest $request, LandingPagePro $landing_page_pro): RedirectResponse
    {
        abort_if(request()->user()->is(['salesman', 'uploader']), 403, 'You don\'t have permission.');

        $landing_page_pro->update([
            ...$request->safe()->except(['items']),
            'published_at' => $this->resolvePublishedAt($request->boolean('is_published'), $request->input('published_at')),
        ]);

        $this->syncItems($landing_page_pro, $request->input('items', []));

        return to_route('admin.landing-page-pros.index')->withSuccess('Landing Page Pro updated successfully.');
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
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'sku'])
            ->map(fn (Product $product): array => [
                'id' => $product->id,
                'label' => $product->name,
            ])
            ->values();
    }
}
