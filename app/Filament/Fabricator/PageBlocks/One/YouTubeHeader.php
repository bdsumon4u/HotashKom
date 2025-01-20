<?php

namespace App\Filament\Fabricator\PageBlocks\One;

use App\Filament\Fabricator\PageBlocks\HasBlockName;
use Filament\Facades\Filament;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Forms\Set;
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
                TextInput::make('youtube_link'),
            ]);
    }

    public static function mutateData(array $data): array
    {
        return $data;
    }

    public static function default(?PageContract $record, Get $get, Set $set): array
    {
        return [
            'data' => [
                'headline' => 'ওজন হ্রাসে, ডায়াবেটিস নিয়ন্ত্রণে, হৃদরোগের ঝুঁকি কমাতে, শরীরের ইমোনিটি এবং এনার্জি সাপোর্টর জন্য লাল চাল হোক আপনার আমার নিত্য দিনের খাবারের অংশ।',
            ],
            'type' => static::getBlockName(),
        ];
    }
}
