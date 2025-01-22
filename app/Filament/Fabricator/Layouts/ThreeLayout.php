<?php

namespace App\Filament\Fabricator\Layouts;

use App\Filament\Fabricator\PageBlocks\Three\BoxList;
use App\Filament\Fabricator\PageBlocks\Three\CheckList;
use App\Filament\Fabricator\PageBlocks\Three\CountdownHeader;
use App\Filament\Fabricator\PageBlocks\Three\NormalText;
use App\Filament\Fabricator\PageBlocks\Three\YouTube;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Z3d0X\FilamentFabricator\Layouts\Layout;
use Z3d0X\FilamentFabricator\Models\Contracts\Page as PageContract;

class ThreeLayout extends Layout
{
    protected static ?string $name = 'three';

    public static function getPageBlocks(?PageContract $record, Get $get, Set $set): array
    {
        return [
            CountdownHeader::default([
                'deadline' => now()->addWeek()->toDateTimeString(),
                'title' => 'চোখের সুরক্ষায় ব্লু-কাট চশমার বিকল্প আর কিছু নেই!',
                'subtitle' => 'আমরা দিচ্ছি এ গ্রেড এর অরিজিনাল আই প্রো ব্রান্ডের ব্লু-কাট গ্লাস। সাথে প্রিমিয়াম ফ্রেম।',
                'note' => '*স্টক শেষ হওয়ার আগে এখনি অর্ডার করুন*',
            ]),
            YouTube::default([
                'youtube_link' => 'https://youtu.be/SdoquBK0ceQ',
            ]),
            CheckList::default([
                'title' => 'আমাদের প্যাকেজে যা পাচ্ছেনঃ',
                'content' => '<ul><li><strong>ওরিজিনাল আই-প্রো ব্রান্ড লেন্স</strong></li><li><strong>প্রিমিয়াম লাইট-ওয়েট ফ্রেম</strong></li><li><strong>একটি লেন্স ক্লিনার স্প্রে</strong></li><li><strong>দুইটি ক্লিনিং কটন</strong></li><li><strong>লেজার টেস্টিং কিট</strong></li><li><strong>একটি উন্নতমানের চশমা জিপার বক্স</strong></li></ul>',
            ]),
            NormalText::default([
                'content' => '
                    <p><span style="font-size: 36px; color: #dedede;"><strong>রেগুলার প্রাইস <span style="color: #f1c40f;"><s>১৫৫০</s></span> টাকা</strong></span></p>
                    <h1><span style="font-size: 48px; color: #dedede;"><string>অফার প্রাইস মাত্র <span style="text-decoration: underline;"><span style="color: #f1c40f; text-decoration: underline;">৯৯০</span></span> টাকা</strong></span></h1>
                    <p><span style="font-size: 28px; color: #dedede;"><strong>ডেলিভারি চার্জ সারা বাংলাদেশে মাত্র ১০০ টাকা!</strong></span></p>
                ',
            ]),
            BoxList::default([
                'title' => 'ব্লু-কাট চশমার উপকারিতা',
                'content' => '<ul><li><strong>ওরিজিনাল আই-প্রো ব্রান্ড লেন্স</strong></li><li><strong>প্রিমিয়াম লাইট-ওয়েট ফ্রেম</strong></li><li><strong>একটি লেন্স ক্লিনার স্প্রে</strong></li><li><strong>দুইটি ক্লিনিং কটন</strong></li><li><strong>লেজার টেস্টিং কিট</strong></li><li><strong>একটি উন্নতমানের চশমা জিপার বক্স</strong></li></ul>',
            ]),
        ];
    }
}
