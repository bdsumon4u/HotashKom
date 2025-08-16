<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\HomeSection;
use Illuminate\Support\Collection;
use Livewire\Component;

final class BannerSection extends Component
{
    public Collection $categories;

    public ?HomeSection $section;

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
                    'link' => old('data.columns.link.'.$i, $pseudoColumns['link'][$i] ?? ''),
                    'categories' => old('data.columns.categories.'.$i, ((array) ($pseudoColumns['categories'] ?? []))[$i] ?? []),
                ];
            }
        }
    }

    public function addColumn(): void
    {
        $remainingWidth = 12 - array_sum(array_column($this->columns, 'width'));
        $defaultWidth = max(1, min(12, $remainingWidth > 0 ? $remainingWidth : 12));

        $this->columns[] = [
            'image' => null,
            'width' => $defaultWidth,
            'animation' => 'fade-right',
            'link' => '',
            'categories' => [],
        ];

        // Dispatch event to reinitialize UI components
        $this->dispatch('column-added', ['count' => count($this->columns)]);
    }

    public function removeColumn(int $index): void
    {
        unset($this->columns[$index]);
        $this->columns = array_values($this->columns);

        // Dispatch event to reinitialize UI components
        $this->dispatch('column-removed', ['count' => count($this->columns)]);
    }

    public function removeImage(int $index): void
    {
        if (isset($this->columns[$index])) {
            $this->columns[$index]['image'] = null;

            // Force a re-render by dispatching an event
            $this->dispatch('image-removed', ['index' => $index]);
        }
    }

    public function updateImage(int $index, string $imagePath): void
    {
        if (isset($this->columns[$index])) {
            $this->columns[$index]['image'] = $imagePath;

            // Force a re-render by dispatching an event
            $this->dispatch('image-updated', ['index' => $index, 'path' => $imagePath]);
        }
    }

    public function duplicateColumn(int $index): void
    {
        if (isset($this->columns[$index])) {
            $column = $this->columns[$index];
            // Don't duplicate the image, let user select a new one
            $column['image'] = null;
            $this->columns[] = $column;
        }
    }

    public function updateColumnOrder(array $orderedIds): void
    {
        $reorderedColumns = [];
        foreach ($orderedIds as $id) {
            if (isset($this->columns[$id])) {
                $reorderedColumns[] = $this->columns[$id];
            }
        }
        $this->columns = $reorderedColumns;
    }

    public function getTotalWidth(): int
    {
        return array_sum(array_column($this->columns, 'width'));
    }

    public function normalizeWidths(): void
    {
        $totalWidth = $this->getTotalWidth();
        if ($totalWidth > 12) {
            // Proportionally reduce all widths to fit within 12
            foreach ($this->columns as $i => $column) {
                $this->columns[$i]['width'] = max(1, floor(($column['width'] / $totalWidth) * 12));
            }
        }
    }

    public function setLayout(string $layout): void
    {
        switch ($layout) {
            case 'full':
                $this->columns = [[
                    'image' => null,
                    'width' => 12,
                    'animation' => 'fade-up',
                    'link' => '',
                    'categories' => [],
                ]];
                break;
            case 'half':
                $this->columns = [
                    [
                        'image' => null,
                        'width' => 6,
                        'animation' => 'fade-left',
                        'link' => '',
                        'categories' => [],
                    ],
                    [
                        'image' => null,
                        'width' => 6,
                        'animation' => 'fade-right',
                        'link' => '',
                        'categories' => [],
                    ],
                ];
                break;
            case 'third':
                $this->columns = [
                    [
                        'image' => null,
                        'width' => 4,
                        'animation' => 'fade-left',
                        'link' => '',
                        'categories' => [],
                    ],
                    [
                        'image' => null,
                        'width' => 4,
                        'animation' => 'fade-up',
                        'link' => '',
                        'categories' => [],
                    ],
                    [
                        'image' => null,
                        'width' => 4,
                        'animation' => 'fade-right',
                        'link' => '',
                        'categories' => [],
                    ],
                ];
                break;
            case 'asymmetric-8-4':
                $this->columns = [
                    [
                        'image' => null,
                        'width' => 8,
                        'animation' => 'fade-right',
                        'link' => '',
                        'categories' => [],
                    ],
                    [
                        'image' => null,
                        'width' => 4,
                        'animation' => 'fade-left',
                        'link' => '',
                        'categories' => [],
                    ],
                ];
                break;
            case 'asymmetric-4-8':
                $this->columns = [
                    [
                        'image' => null,
                        'width' => 4,
                        'animation' => 'fade-right',
                        'link' => '',
                        'categories' => [],
                    ],
                    [
                        'image' => null,
                        'width' => 8,
                        'animation' => 'fade-left',
                        'link' => '',
                        'categories' => [],
                    ],
                ];
                break;
            case 'asymmetric':
                // Keep the old asymmetric for backward compatibility
                $this->columns = [
                    [
                        'image' => null,
                        'width' => 8,
                        'animation' => 'fade-right',
                        'link' => '',
                        'categories' => [],
                    ],
                    [
                        'image' => null,
                        'width' => 4,
                        'animation' => 'fade-left',
                        'link' => '',
                        'categories' => [],
                    ],
                ];
                break;
        }
    }

    public function render()
    {
        return view('livewire.banner-section');
    }
}
