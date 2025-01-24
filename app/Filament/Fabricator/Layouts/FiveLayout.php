<?php

namespace App\Filament\Fabricator\Layouts;

use App\Filament\Fabricator\PageBlocks\Five\AdditionalImages;
use App\Filament\Fabricator\PageBlocks\Five\CheckList;
use App\Filament\Fabricator\PageBlocks\Five\Header;
use App\Filament\Fabricator\PageBlocks\Five\NormalText;
use Filament\Facades\Filament;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Z3d0X\FilamentFabricator\Layouts\Layout;
use Z3d0X\FilamentFabricator\Models\Contracts\Page as PageContract;

class FiveLayout extends Layout
{
    protected static ?string $name = 'five';

    public static function getPageBlocks(?PageContract $record, Get $get, Set $set): array
    {
        return [
            Header::default([
                'thumbnail' => Filament::getTenant()->base_image->src,
                'title' => 'Rice Skin Beauty Serum 15ML',
                'content' => '<p>ব্রনের কারনে ত্বকে গর্তের মতো হয়, আর একে pores বলে। আর এই pores দূর করতে সবচেয়ে বেস্ট হলো <span style="color: red;">Rice Serum</span>. এটা শুধু pores remove করে না, স্কিনটাইট ও গ্লোয়িং করে Damage Skin Repair করে।</p>',
            ]),
            NormalText::default([
                'content' => '
                    <h1>মাত্র ৭*দি*নে চেহারায় কমবে ৫*বছ*র</h1>
                    <p>ব্রনের কারনে ত্বকে গর্তের মতো হয়ে, আর একে pores বলে। আর এই pores দূর করতে সবচেয়ে Best হলো Rice Serum. এটি ছেলে মেয়ে সবাই ব্যবহার করতে পারবে। কোণ প্রকার সাইড ইফেক্ট নেই।</p>
                ',
            ]),
            CheckList::default([
                'title' => 'রাইস সিরাম কেন ব্যবহার করবেন?',
                'content' => '<ul>
                    <li>আপনার ত্বক ফর্সা,উজ্জল,গ্লোয়িং করে।</li>
                    <li>ত্বকের পোর মিনিমাইজ করে।</li>
                    <li>ত্বক মসৃণ করে।</li>
                    <li>ত্বকের কালো দাগ দূর করে।</li>
                    <li>ত্বকে পুষ্টি যোগায়।</li>
                    <li>ড্রাই ত্বক ময়েশ্চারাইজ করে।</li>
                    <li>কোরিয়ান মেয়েদের মত স্কিনকে গ্লাসস্ক্রিন করবে।</li>
                    <li>সারাদিন আপনার ত্বককে টানটান রাখে।</li>
                    <li>সূর্যের ক্ষতিকর প্রভাব থেকে ত্বক রক্ষা করে।</li>
                    <li>ত্বক প্রদাহ থেকে বাচায়।</li>
                </ul>',
            ]),
            AdditionalImages::default([
                'title' => 'ব্যবহারের পদ্ধতি',
                'images' => Filament::getTenant()->additional_images->map->src->toArray(),
                'description' => '<p>সিরামের ২/৩ ফোঁটা হাতে নিয়ে আঙ্গুল দিয়ে হালকা Dabbing motion এ massage করবেন।</p>',
            ]),
        ];
    }
}
