<?php

namespace App\Filament\Fabricator\PageBlocks\Four;

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
                    <svg aria-hidden="true" class="e-font-icon-svg e-fas-check"
                        viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M173.898 439.404l-166.4-166.4c-9.997-9.997-9.997-26.206 0-36.204l36.203-36.204c9.997-9.998 26.207-9.998 36.204 0L192 312.69 432.095 72.596c9.997-9.997 26.207-9.997 36.204 0l36.203 36.204c9.997 9.997 9.997 26.206 0 36.204l-294.4 294.401c-9.998 9.997-26.207 9.997-36.204-.001z">
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

        return $listHtml;
    }
}
