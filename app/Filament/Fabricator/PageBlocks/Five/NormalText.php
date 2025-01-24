<?php

namespace App\Filament\Fabricator\PageBlocks\Five;

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
        $data['content'] = static::transformHtml($data['content']);

        return $data;
    }

    private static function transformHtml(string $htmlContent): string
    {
        // Step 1: Add "elementor-headline e-animated" classes to all heading tags
        $htmlContent = preg_replace_callback(
            '/<(h[1-6])(.*?)>/i', // Match all heading tags (h1 to h6) with optional attributes
            function ($matches) {
                $tag = $matches[1]; // Heading tag (e.g., h1, h2, etc.)
                $attributes = $matches[2]; // Existing attributes
                // Add the class if it doesn't already exist
                if (str_contains($attributes, 'class=')) {
                    // Append the classes to existing class attributes
                    return "<{$tag}".preg_replace('/class="(.*?)"/', 'class="$1 elementor-headline e-animated"', $attributes).'>';
                } else {
                    // Add a new class attribute
                    return "<{$tag}{$attributes} class=\"elementor-headline e-animated\">";
                }
            },
            $htmlContent
        );

        // Step 2: Replace <span style="text-decoration: underline;">...</span> content inside heading tags
        $htmlContent = preg_replace_callback(
            '/<(h[1-6][^>]*?)>(.*?)<\/h[1-6]>/is', // Match heading tags and their content
            function ($matches) {
                $headingTag = $matches[1]; // The opening tag (e.g., h1, h2, etc.)
                $content = $matches[2]; // Content inside the heading tag

                // Replace the <span> with the new structure inside the heading tag
                $transformedContent = preg_replace(
                    '/<span style="text-decoration: underline;">(.*?)<\/span>/s',
                    '<span class="elementor-headline-dynamic-wrapper elementor-headline-text-wrapper">
                        <span class="elementor-headline-dynamic-text elementor-headline-text-active">$1</span>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 150" preserveAspectRatio="none">
                            <path d="M3,146.1c17.1-8.8,33.5-17.8,51.4-17.8c15.6,0,17.1,18.1,30.2,18.1c22.9,0,36-18.6,53.9-18.6 c17.1,0,21.3,18.5,37.5,18.5c21.3,0,31.8-18.6,49-18.6c22.1,0,18.8,18.8,36.8,18.8c18.8,0,37.5-18.6,49-18.6c20.4,0,17.1,19,36.8,19 c22.9,0,36.8-20.6,54.7-18.6c17.7,1.4,7.1,19.5,33.5,18.8c17.1,0,47.2-6.5,61.1-15.6"></path>
                        </svg>
                    </span>',
                    $content
                );

                return "<{$headingTag}>{$transformedContent}</{$headingTag}>";
            },
            (string) $htmlContent
        );

        return $htmlContent;
    }
}
