<?php

namespace App\Filament\Fabricator\PageBlocks\Three;

use App\Filament\Fabricator\PageBlocks\HasBlockName;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Z3d0X\FilamentFabricator\PageBlocks\PageBlock;

class CheckList extends PageBlock
{
    use HasBlockName;

    public static function getBlockSchema(): Block
    {
        return Block::make(static::getBlockName())
            ->schema([
                TextInput::make('title')
                    ->required(),
                RichEditor::make('content')
                    ->required(),
            ]);
    }

    public static function mutateData(array $data): array
    {
        $data['content'] = static::transformListHtmlQuick($data['content']);

        return $data;
    }

    private static function transformListHtmlQuick(?string $listHtml): ?string
    {
        if (! $listHtml) {
            return $listHtml;
        }

        // Define the SVG icon
        $icon = '<span class="elementor-icon-list-icon">
                    <svg aria-hidden="true" class="e-font-icon-svg e-fas-check-circle" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
                        <path d="M504 256c0 136.967-111.033 248-248 248S8 392.967 8 256 119.033 8 256 8s248 111.033 248 248zM227.314 387.314l184-184c6.248-6.248 6.248-16.379 0-22.627l-22.627-22.627c-6.248-6.249-16.379-6.249-22.628 0L216 308.118l-70.059-70.059c-6.248-6.248-16.379-6.248-22.628 0l-22.627 22.627c-6.248 6.248-6.248 16.379 0 22.627l104 104c6.249 6.249 16.379 6.249 22.628.001z">
                        </path>
                    </svg>
                </span>';

        // Add classes to UL or OL tags
        $listHtml = preg_replace('/<(ul|ol)>/', '<$1 class="elementor-icon-list-items">', $listHtml);

        // Add classes to LI tags and wrap content
        $listHtml = preg_replace_callback('/<li>(.*?)<\/li>/s', function ($matches) use ($icon) {
            $content = $matches[1];

            return '<li class="elementor-icon-list-item">'.$icon.'<span class="elementor-icon-list-text">'.$content.'</span></li>';
        }, $listHtml);

        return $listHtml;
    }
}
