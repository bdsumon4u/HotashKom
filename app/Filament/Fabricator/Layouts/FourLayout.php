<?php

namespace App\Filament\Fabricator\Layouts;

use App\Filament\Fabricator\PageBlocks\Four\AdditionalImages;
use App\Filament\Fabricator\PageBlocks\Four\CheckList;
use App\Filament\Fabricator\PageBlocks\Four\Elements;
use App\Filament\Fabricator\PageBlocks\Four\Features;
use App\Filament\Fabricator\PageBlocks\Four\Header;
use App\Filament\Fabricator\PageBlocks\Four\NormalText;
use Filament\Facades\Filament;
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
                'thumbnail' => Filament::getTenant()->base_image->src,
                'title' => 'চিংড়ি বালাচাও',
                'content' => '
                    <p><span style="font-size: 24px; color: #34495e;">“চিংড়ি বালাচাও” খুবই সুস্বাদু ও মুখরোচক একটি খাবার। নামটা যেমন অন্যরকম, খেতেও অসম্ভব মজাদার। বালাচাও হচ্ছে এক প্রকার রেডি টু ইট ফুড, যা মুলত চিংড়ি, পেয়াজ , রসুন, শুকনো মরিচ ও মশলার একটি মিশ্রণ। বালাচাও হচ্ছে কক্সবাজার ও চট্টগ্রামের একটি জনপ্রিয় ঐতিহ্যবাহী খাবার।</span></p>
                    <p><span style="font-size: 22px; color: #000000;">হোম মেইড এবং রেডি টু ইট ফুড</span></p>
                ',
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
                'title' => 'যেভাবে খাওয়া যায়',
                'content' => '<ul>
                    <li>মুড়ি মাখা বা মুড়ি ভর্তার সাথে।</li>
                    <li>আলু ভর্তার সাথে মিশিয়ে।</li>
                    <li>বেগুন ভর্তার সাথে মিশিয়ে।</li>
                    <li>গরম ধোয়া ওঠা সাদা ভাতের সাথে।</li>
                    <li>চানাচুরের মতো সরাসরি।</li>
                    <li>গরম গরম খুদের ভাতের সাথে।</li>
                    <li>ভূনা খিচুড়ি ও পোলাওয়ের সাথে।</li>
                </ul>',
            ]),
            NormalText::default([
                'content' => '
                    <h2><span style="font-size: 48px;">৫০০ গ্রাম এর পূর্বমূল্য <span style="color: #ff0000;"><s>৯৫০</s></span> এখন ছাড়ে ৮০০ টাকা</span></h2>
                    <hr>
                    <h2><span style="font-size: 48px;">১ কেজি এর পূর্বমূল্য <span style="color: #ff0000;"><s>১৮০০</s></span> এখন ছাড়ে ১৫৫০ টাকা</span></h2>
                ',
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
            AdditionalImages::default([
                'title' => 'কাস্টমার রিভিউ',
                'images' => Filament::getTenant()->additional_images->map->src->toArray(),
            ]),
        ];
    }
}
