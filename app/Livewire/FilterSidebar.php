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

    public function mount(
        ?int $categoryId = null,
        ?int $brandId = null,
        ?string $search = null,
        bool $hideCategoryFilter = false,
        array $selectedCategories = [],
        array $selectedOptions = [],
    ): void
    {
        $this->categoryId = $categoryId;
        $this->brandId = $brandId;
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
            'brandId' => $this->brandId,
            'search' => $this->search,
            'hideCategoryFilter' => $this->hideCategoryFilter,
        ]);
    }
}
