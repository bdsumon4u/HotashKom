<?php

namespace App\Livewire;

use App\Models\HomeSection;
use Illuminate\Support\Collection;
use Livewire\Component;

class BannerSection extends Component
{
    public Collection $categories;

    public ?HomeSection $section;

    public array $columns = [];

    public function mount()
    {
        if (isset($this->section)) {
            $pseudoColumns = (array) $this->section->data->columns;
            foreach ($pseudoColumns['width'] as $i => $width) {
                $this->columns[] = [
                    'image' => $pseudoColumns['image'][$i] ?? null,
                    'width' => $width,
                    'animation' => $pseudoColumns['animation'][$i] ?? null,
                    'link' => $pseudoColumns['link'][$i] ?? null,
                    'categories' => $pseudoColumns['categories'][$i] ?? null,
                ];
            }
        }
    }

    public function addColumn()
    {
        $this->columns[] = [
            'image' => null,
            'width' => 12,
            'animation' => 'fade-right',
            'link' => '#',
            'categories' => [],
        ];
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
