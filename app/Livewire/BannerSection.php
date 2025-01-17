<?php

namespace App\Livewire;

use App\Models\HomeSection;
use Illuminate\Support\Collection;
use Livewire\Component;

class BannerSection extends Component
{
    public Collection $categories;

    public ?HomeSection $section = null;

    public array $columns = [];

    public function mount(): void
    {
        if (isset($this->section)) {
            $pseudoColumns = (array) $this->section->data->columns;
            foreach ($pseudoColumns['width'] as $i => $width) {
                $this->columns[] = [
                    'image' => $pseudoColumns['image'][$i] ?? null,
                    'width' => old('data.columns.width.'.$i, $width),
                    'animation' => old('data.columns.animation.'.$i, $pseudoColumns['animation'][$i] ?? 'fade-right'),
                    'link' => old('data.columns.link.'.$i, $pseudoColumns['link'][$i] ?? '#'),
                    'categories' => old('data.columns.categories.'.$i, ((array)($pseudoColumns['categories'] ?? []))[$i] ?? []),
                ];
            }
        }
    }

    public function addColumn(): void
    {
        $this->columns[] = [
            'image' => null,
            'width' => 12,
            'animation' => 'fade-right',
            'link' => '',
            'categories' => [],
        ];
    }

    public function removeColumn($i): void
    {
        unset($this->columns[$i]);
        $this->columns = array_values($this->columns);
    }

    public function render()
    {
        return view('livewire.banner-section');
    }
}
