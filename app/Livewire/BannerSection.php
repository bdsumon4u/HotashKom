<?php

namespace App\Livewire;

use Illuminate\Support\Collection;
use Livewire\Component;

class BannerSection extends Component
{
    public Collection $categories;

    public array $columns = [];

    public function mount()
    {
        $this->columns[] = [
            'image' => null,
            'width' => 12,
            'animation' => 'fade-right',
            'link' => '#',
            'categories' => [],
        ];
    }

    public function save()
    {
        $this->validate([
            'columns.*.image' => 'required|url',
            'columns.*.width' => 'required|numeric',
            'columns.*.animation' => 'required|string',
            'columns.*.link' => 'nullable|string',
            'columns.*.categories' => 'nullable|array',
        ]);

        dd($this->columns);
    }

    public function removeColumn($i)
    {
        unset($this->columns[$i]);
        $this->columns = array_values($this->columns);
    }

    public function render()
    {
        return view('livewire.banner-section');
    }
}
