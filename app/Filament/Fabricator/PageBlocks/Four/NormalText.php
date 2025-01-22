<?php

namespace App\Filament\Fabricator\PageBlocks\Four;

use AmidEsfahani\FilamentTinyEditor\TinyEditor;
use App\Filament\Fabricator\PageBlocks\HasBlockName;
use Filament\Forms\Components\Builder\Block;
use Z3d0X\FilamentFabricator\PageBlocks\PageBlock;

class NormalText extends PageBlock
{
    use HasBlockName;

    public static function getBlockSchema(): Block
    {
        return Block::make(static::getBlockName())
            ->schema([
                TinyEditor::make('content')
                    ->required(),
            ]);
    }

    public static function mutateData(array $data): array
    {
        $data['content'] = preg_replace('/<hr\s*\/?>/i', '
        <div class="elementor-element elementor-element-a1112ef elementor-widget-divider--separator-type-pattern elementor-widget-divider--view-line elementor-widget elementor-widget-divider">
            <div class="elementor-divider"
                style="--divider-pattern-url: url(&quot;data:image/svg+xml,%3Csvg xmlns=&#039;http://www.w3.org/2000/svg&#039; preserveAspectRatio=&#039;none&#039; overflow=&#039;visible&#039; height=&#039;100%&#039; viewBox=&#039;0 0 24 24&#039; fill=&#039;none&#039; stroke=&#039;black&#039; stroke-width=&#039;2&#039; stroke-linecap=&#039;square&#039; stroke-miterlimit=&#039;10&#039;%3E%3Cpath d=&#039;M0,6c6,0,6,13,12,13S18,6,24,6&#039;/%3E%3C/svg%3E&quot;);">
                <span class="elementor-divider-separator">
                </span>
            </div>
        </div>
        ', $data['content']);

        return $data;
    }
}
