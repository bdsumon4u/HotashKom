<?php

namespace App\Filament\Fabricator\PageBlocks\One;

use App\Filament\Fabricator\PageBlocks\HasBlockName;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Z3d0X\FilamentFabricator\Models\Contracts\Page as PageContract;
use Z3d0X\FilamentFabricator\PageBlocks\PageBlock;

class TwoColList extends PageBlock
{
    use HasBlockName;

    public static function getBlockSchema(): Block
    {
        return Block::make(static::getBlockName())
            ->schema([
                Group::make([
                    TextInput::make('title'),
                    RichEditor::make('content'),
                ])
                ->columnSpanFull(),
                Group::make([
                    TextInput::make('left_title'),
                    RichEditor::make('left_content'),
                ]),
                Group::make([
                    TextInput::make('right_title'),
                    RichEditor::make('right_content'),
                ]),
            ])
            ->columns(2);
    }

    public static function mutateData(array $data): array
    {
        $data['content'] = static::transformListHtmlQuick($data['content']);
        $data['left_content'] = static::transformListHtmlQuick($data['left_content']);
        $data['right_content'] = static::transformListHtmlQuick($data['right_content']);

        return $data;
    }

    public static function default(array $data): array
    {
        return [
            'data' => $data,
            'type' => static::getBlockName(),
        ];
    }

    private static function transformListHtmlQuick(?string $listHtml): ?string
    {
        if (!  $listHtml) {
            return $listHtml;
        }

        // Define the SVG icon
        $icon = '<span class="elementor-icon-list-icon">
                    <svg aria-hidden="true" class="e-font-icon-svg e-far-hand-point-right" viewBox="0 0 512 512"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M428.8 137.6h-86.177a115.52 115.52 0 0 0 2.176-22.4c0-47.914-35.072-83.2-92-83.2-45.314 0-57.002 48.537-75.707 78.784-7.735 12.413-16.994 23.317-25.851 33.253l-.131.146-.129.148C135.662 161.807 127.764 168 120.8 168h-2.679c-5.747-4.952-13.536-8-22.12-8H32c-17.673 0-32 12.894-32 28.8v230.4C0 435.106 14.327 448 32 448h64c8.584 0 16.373-3.048 22.12-8h2.679c28.688 0 67.137 40 127.2 40h21.299c62.542 0 98.8-38.658 99.94-91.145 12.482-17.813 18.491-40.785 15.985-62.791A93.148 93.148 0 0 0 393.152 304H428.8c45.435 0 83.2-37.584 83.2-83.2 0-45.099-38.101-83.2-83.2-83.2zm0 118.4h-91.026c12.837 14.669 14.415 42.825-4.95 61.05 11.227 19.646 1.687 45.624-12.925 53.625 6.524 39.128-10.076 61.325-50.6 61.325H248c-45.491 0-77.21-35.913-120-39.676V215.571c25.239-2.964 42.966-21.222 59.075-39.596 11.275-12.65 21.725-25.3 30.799-39.875C232.355 112.712 244.006 80 252.8 80c23.375 0 44 8.8 44 35.2 0 35.2-26.4 53.075-26.4 70.4h158.4c18.425 0 35.2 16.5 35.2 35.2 0 18.975-16.225 35.2-35.2 35.2zM88 384c0 13.255-10.745 24-24 24s-24-10.745-24-24 10.745-24 24-24 24 10.745 24 24z">
                        </path>
                    </svg>
                </span>';

        // Add classes to UL or OL tags
        $listHtml = preg_replace('/<(ul|ol)>/', '<$1 class="elementor-icon-list-items">', $listHtml);

        // Add classes to LI tags and wrap content
        $listHtml = preg_replace_callback('/<li>(.*?)<\/li>/s', function ($matches) use ($icon) {
            $content = $matches[1];
            return '<li class="elementor-icon-list-item">' . $icon . '<span class="elementor-icon-list-text">' . $content . '</span></li>';
        }, $listHtml);

        return $listHtml;
    }
}
