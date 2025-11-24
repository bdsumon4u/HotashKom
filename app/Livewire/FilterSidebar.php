<?php

namespace App\Livewire;

use App\Models\Category;
use App\Traits\HasProductFilters;
use Livewire\Component;

class FilterSidebar extends Component
{
    use HasProductFilters;

    public ?int $categoryId = null;

    public ?int $brandId = null;

    public ?string $search = null;

    public bool $hideCategoryFilter = false;

    public array $selectedCategories = [];

    public array $selectedOptions = [];

    public ?string $categorySlug = null;

    public ?string $brandSlug = null;

    public function mount(
        ?int $categoryId = null,
        ?string $categorySlug = null,
        ?int $brandId = null,
        ?string $brandSlug = null,
        ?string $search = null,
        bool $hideCategoryFilter = false,
        array $selectedCategories = [],
        array $selectedOptions = [],
    ): void {
        $this->categoryId = $categoryId;
        $this->categorySlug = $categorySlug;
        $this->brandId = $brandId;
        $this->brandSlug = $brandSlug;
        $this->search = $search;
        $this->hideCategoryFilter = $hideCategoryFilter;
        $this->selectedCategories = array_map('intval', array_filter($selectedCategories));
        $this->selectedOptions = array_map('intval', array_filter($selectedOptions));
    }

    public function render()
    {
        $category = $this->categoryId ? Category::find($this->categoryId) : null;
        $filterData = $this->getProductFilterData($category);

        $this->dispatch('filter-sidebar-loaded');

        return view('livewire.filter-sidebar', $filterData + [
            'categoryId' => $this->categoryId,
            'categorySlug' => $this->categorySlug,
            'brandId' => $this->brandId,
            'brandSlug' => $this->brandSlug,
            'search' => $this->search,
            'hideCategoryFilter' => $this->hideCategoryFilter,
        ]);
    }
}
