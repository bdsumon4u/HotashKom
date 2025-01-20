<?php

namespace App\Filament\Fabricator\PageBlocks\One;

use App\Filament\Fabricator\PageBlocks\HasBlockName;
use Filament\Facades\Filament;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Forms\Set;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use Z3d0X\FilamentFabricator\PageBlocks\PageBlock;
use Z3d0X\FilamentFabricator\Models\Contracts\Page as PageContract;

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

    public static function mutateData(array $data): array
    {
        $data['headline'] = static::highlightWords($data['headline'], $data['highlights']);

        return $data;
    }

    public static function default(?PageContract $record, Get $get, Set $set): array
    {
        return [
            'data' => [
                'headline' => 'ওজন হ্রাসে, ডায়াবেটিস নিয়ন্ত্রণে, হৃদরোগের ঝুঁকি কমাতে, শরীরের ইমোনিটি এবং এনার্জি সাপোর্টর জন্য লাল চাল হোক আপনার আমার নিত্য দিনের খাবারের অংশ।',
                'youtube_link' => 'https://youtu.be/Wuls05qIKuU?si=V87zAjhQ8Da7GyZ_',
            ],
            'type' => static::getBlockName(),
        ];
    }

    private static function highlightWords(string $text, ?string $highlights): string
    {
        if (! $highlights) {
            return $text;
        }

        // Escape special characters in the words for regex
        $escapedWords = array_map('trim', array_map('preg_quote', explode('|', $highlights)));

        // Create a regex pattern to match the words
        $pattern = '/(' . implode('|', $escapedWords) . ')/i';

        // Replace the matched words with a <div>-wrapped version
        $replacement = '<span class="elementor-headline-dynamic-wrapper elementor-headline-text-wrapper">
                            <span class="elementor-headline-dynamic-text elementor-headline-text-active">
                                $1
                            </span>
                        </span>';

        return preg_replace($pattern, $replacement, $text);
    }
}
