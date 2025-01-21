<?php

namespace App\Filament\Fabricator\Layouts;

use App\Filament\Fabricator\PageBlocks\Four\CheckList;
use App\Filament\Fabricator\PageBlocks\Four\CustomerReview;
use App\Filament\Fabricator\PageBlocks\Four\Elements;
use App\Filament\Fabricator\PageBlocks\Four\Features;
use App\Filament\Fabricator\PageBlocks\Four\Header;
use App\Filament\Fabricator\PageBlocks\Four\NormalText;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Z3d0X\FilamentFabricator\Layouts\Layout;
use Z3d0X\FilamentFabricator\Models\Contracts\Page as PageContract;

class FourLayout extends Layout
{
    protected static ?string $name = 'four';

    public static function getPageBlocks(?PageContract $record, Get $get, Set $set): array
    {
        return [
            Header::default([
                'title' => 'চিংড়ি বালাচাও',
                'content' => '',
            ]),
            Elements::default([
                'title' => 'কি কি উপাদানে তৈরি?',
                'items' => [
                    ['name' => 'চিংড়ি শুটকি'],
                    ['name' => 'পেঁয়াজ বেরেস্তা'],
                    ['name' => 'রসুন ভাজা'],
                    ['name' => 'মরিচের গুঁড়া'],
                    ['name' => 'সিক্রেট মশলা'],
                ],
            ]),
            CheckList::default([

            ]),
            NormalText::default([
                
            ]),
            Features::default([
                'title' => 'আমাদের থেকে কেন নিবেন ?',
                'items' => [
                    ['name' => 'অভিজ্ঞ বাবুর্চির ফর্মুলায় তৈরি'],
                    ['name' => 'হোমমেইড প্রক্রিয়ায় স্বাস্থ্যসম্মত ভাবে তৈরি'],
                    ['name' => 'বাছাইকৃত সম্পূর্ণ বালুমুক্ত চিংড়ি শুটকি'],
                    ['name' => 'অরিজিনাল বালাচাও এর স্বাদ'],
                ],
            ]),
            CustomerReview::default([
                'title' => 'গ্রাহকের পর্যালোচনা',
            ]),
        ];
    }
}