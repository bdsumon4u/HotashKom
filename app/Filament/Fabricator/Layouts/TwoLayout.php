<?php

namespace App\Filament\Fabricator\Layouts;

use App\Filament\Fabricator\PageBlocks\Two\RoundedHeading;
use App\Filament\Fabricator\PageBlocks\Two\YouTubePrice;
use Filament\Facades\Filament;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Z3d0X\FilamentFabricator\Layouts\Layout;
use Z3d0X\FilamentFabricator\Models\Contracts\Page as PageContract;

class TwoLayout extends Layout
{
    protected static ?string $name = 'two';

    public static function getPageBlocks(?PageContract $record, Get $get, Set $set): array
    {
        return [
            RoundedHeading::default([
                'thumbnail' => Filament::getTenant()->base_image->src,
                'heading' => '৩ পিস টি-শার্ট মাত্র ১১৯০ টাকা',
                'subheading' => 'Premium Quality Jearsey T Shart Package কোয়ালিটি প্রোডাক্ট কখনো সস্থায় পাওয়া যায় না তবুও আমরা এই মুল্যে দিচ্ছি সর্বোচ্চ কোয়ালিটির প্রোডাক্ট।',
            ]),
            YouTubePrice::default([
                'youtube_link' => 'https://youtu.be/1HtrB9hDve4',
                'title' => 'ZOTO Always comfort',
                'description' => 'আমরা কোয়ালিটি ফেব্রিক্স, কালার গ্যারান্টি এবং সুয়িং গ্যারািন্ট দিচ্ছি, সাথে ১০দিনের রিপ্লেসমেন্ট গ্যারান্টি। শুধু পণ্য বিক্রি নয়, আমরা চাই আপনােক বিক্রয়ত্বর সেবা দান করা এবং আরও সব বাহারি ডিজাইন এবং কোয়ালিটি উপহার দেয়া।',
                'price_text' => '৩ পিস টি-শার্টের মূল্য',
                'price_amount' => '১১৯০',
                'price_subtext' => 'ডেলিভারি চার্জ প্রযোজ্য',
            ]),
        ];
    }
}