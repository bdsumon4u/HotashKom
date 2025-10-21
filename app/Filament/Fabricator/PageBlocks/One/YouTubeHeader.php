<?php

namespace App\Filament\Fabricator\PageBlocks\One;

use App\Filament\Fabricator\PageBlocks\HasBlockName;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Z3d0X\FilamentFabricator\PageBlocks\PageBlock;

class YouTubeHeader extends PageBlock
{
    use HasBlockName;

    public static function getBlockSchema(): Block
    {
        return Block::make(static::getBlockName())
            ->schema([
                TextInput::make('headline')
                    ->required(),
                TextInput::make('highlights')
                    ->hint('| separated'),
                RichEditor::make('description')
                    ->required(),
                TextInput::make('youtube_link'),
            ]);
    }

    #[\Override]
    public static function mutateData(array $data): array
    {
        $data['headline'] = static::highlightWords($data['headline'], $data['highlights']);

        return $data;
    }

    private static function highlightWords(string $text, ?string $highlights): string
    {
        if (! $highlights) {
            return $text;
        }

        // Escape special characters in the words for regex
        $escapedWords = array_map('trim', array_map('preg_quote', explode('|', $highlights)));

        // Create a regex pattern to match the words
        $pattern = '/('.implode('|', $escapedWords).')/i';

        // Replace the matched words with a <div>-wrapped version
        $replacement = '<span class="elementor-headline-dynamic-wrapper elementor-headline-text-wrapper">
                            <span class="elementor-headline-dynamic-text elementor-headline-text-active">
                                $1
                            </span>
                        </span>';

        return preg_replace($pattern, $replacement, $text);
    }
}
