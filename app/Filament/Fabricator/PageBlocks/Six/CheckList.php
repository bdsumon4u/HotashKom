<?php

namespace App\Filament\Fabricator\PageBlocks\Six;

use App\Filament\Fabricator\PageBlocks\HasBlockName;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\FileUpload;
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
                FileUpload::make('image')
                    ->image(),
            ]);
    }

    #[\Override]
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
                    <svg aria-hidden="true" class="e-font-icon-svg e-fas-check-square" viewBox="0 0 448 512" xmlns="http://www.w3.org/2000/svg">
                        <path d="M400 480H48c-26.51 0-48-21.49-48-48V80c0-26.51 21.49-48 48-48h352c26.51 0 48 21.49 48 48v352c0 26.51-21.49 48-48 48zm-204.686-98.059l184-184c6.248-6.248 6.248-16.379 0-22.627l-22.627-22.627c-6.248-6.248-16.379-6.249-22.628 0L184 302.745l-70.059-70.059c-6.248-6.248-16.379-6.248-22.628 0l-22.627 22.627c-6.248 6.248-6.248 16.379 0 22.627l104 104c6.249 6.25 16.379 6.25 22.628.001z">
                        </path>
                    </svg>
                </span>';

        // Add classes to UL or OL tags
        $listHtml = preg_replace('/<(ul|ol)>/', '<$1 class="elementor-icon-list-items">', $listHtml);

        // Add classes to LI tags and wrap content
        $listHtml = preg_replace_callback('/<li>(.*?)<\/li>/s', function ($matches) use ($icon) {
            $content = $matches[1];

            return '<li class="elementor-icon-list-item">'.$icon.'<span class="elementor-icon-list-text">'.$content.'</span></li>';
        }, (string) $listHtml);

        // Transform h1, h2, h3 tags with elementor structure
        $listHtml = preg_replace_callback('/<(h[1-3])>(.*?)<\/\1>/s', function ($matches) {
            $tag = $matches[1];
            $content = $matches[2];

            return '<div class="elementor-element elementor-element-33dce5d elementor-widget elementor-widget-heading" data-id="33dce5d" data-element_type="widget" data-widget_type="heading.default">
                    <div class="elementor-widget-container">
                        <'.$tag.' class="elementor-heading-title elementor-size-default">'.$content.'</'.$tag.'>
                    </div>
                </div>';
        }, (string) $listHtml);

        return $listHtml;
    }
}
