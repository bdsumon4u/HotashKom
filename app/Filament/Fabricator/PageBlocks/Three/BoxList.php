<?php

namespace App\Filament\Fabricator\PageBlocks\Three;

use AmidEsfahani\FilamentTinyEditor\TinyEditor;
use App\Filament\Fabricator\PageBlocks\HasBlockName;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Z3d0X\FilamentFabricator\PageBlocks\PageBlock;

class BoxList extends PageBlock
{
    use HasBlockName;

    public static function getBlockSchema(): Block
    {
        return Block::make(static::getBlockName())
            ->schema([
                TextInput::make('title')
                    ->required(),
                // RichEditor::make('content')
                //     ->required(),
                TinyEditor::make('content')
                    ->fileAttachmentsDisk('public')
                    ->fileAttachmentsVisibility('public')
                    ->fileAttachmentsDirectory('uploads')
                    ->profile('full')
                    // ->rtl() // Set RTL or use ->direction('auto|rtl|ltr')
                    ->columnSpan('full')
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

        // Add classes to UL or OL tags
        $listHtml = preg_replace('/<(ul|ol)>/', '<$1 class="elementor-widget-wrap elementor-element-populated" style="list-style:none">', $listHtml);

        // Add classes to LI tags and wrap content
        $listHtml = preg_replace_callback('/<li>(.*?)<\/li>/s', function ($matches) {
            $content = $matches[1];

            return '<li
                class="elementor-element elementor-element-5b45d67 elementor-invisible elementor-widget elementor-widget-heading"
                data-element_type="widget"
                data-settings="{&quot;_animation&quot;:&quot;fadeInLeft&quot;}"
                data-widget_type="heading.default">
                <div class="elementor-widget-container">
                    <h2 class="elementor-heading-title elementor-size-default">'.$content.'</h2>
                </div>
            </li>';
        }, (string) $listHtml);

        return $listHtml;
    }
}
