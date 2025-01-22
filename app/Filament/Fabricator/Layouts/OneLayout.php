<?php

namespace App\Filament\Fabricator\Layouts;

use App\Filament\Fabricator\PageBlocks\One\ContentColumn;
use App\Filament\Fabricator\PageBlocks\One\YouTubeHeader;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Z3d0X\FilamentFabricator\Layouts\Layout;
use Z3d0X\FilamentFabricator\Models\Contracts\Page as PageContract;

class OneLayout extends Layout
{
    protected static ?string $name = 'one';

    public static function getPageBlocks(?PageContract $record, Get $get, Set $set): array
    {
        return [
            YouTubeHeader::default([
                'headline' => 'ওজন হ্রাসে, ডায়াবেটিস নিয়ন্ত্রণে, হৃদরোগের ঝুঁকি কমাতে, শরীরের ইমোনিটি এবং এনার্জি সাপোর্টর জন্য লাল চাল হোক আপনার আমার নিত্য দিনের খাবারের অংশ।',
                'highlights' => 'লাল চাল | ডায়াবেটিস',
                'description' => 'কিশোরগঞ্জের হাওর অঞ্চল থেকে সংগ্রহ কৃত এই চাল সম্পুর্ন কেমিক্যাল, রং বিহীন, হাফ ও ফুল ফাইবার সমৃদ্ধ। সংগ্রহ করার পর লাল চাল গুলো ঝেড়ে পরিস্কার করে রোদে শুকিয়ে ফুড গ্রেড পলিতে পেকেট করা হয়। বর্তমানে আমাদের কাছে আউশ জাতীয় লাল চাল আছে। ক্রয় করতে চাইলে অর্ডার অথবা কল করুন।',
                'youtube_link' => 'https://youtu.be/Wuls05qIKuU?si=V87zAjhQ8Da7GyZ_',
            ]),
            ContentColumn::default([
                'title' => 'লাল চালের উপকারিতা',
                'content' => 'আমরা সাধারনত ভাত খাই। ভাতই আমাদের প্রধান খাদ্য। ভাত ছাড়া আমরা বাঙালিরা অচল, একথা মোটেও মিথ্যা নয়। আর ভাত যেহেতু খেতেই হবে, তাহলে কেননা আমরা লাল চালের ভাতই খাই। কারণ লাল চালের ভাতে সাদা চালের ভাতের তুলনায় অনেক বেশি পুষ্টি উপাদান রয়েছে। যেমন :- আয়রন, ক্যালসিয়াম, ভিটামিন, এন্টিঅক্সিডেন্ট ও ম্যাগনেসিয়াম এর মতো নানান পুষ্টি উপাদান। যা আমাদের মানবদেহের জন্য খুবই উপকারী।',
                'left_title' => 'আমন ধানের লাল চাল',
                'left_content' => '<ul>
                    <li>শরীরের অতিরিক্ত ওজন হ্রাস</li>
                    <li>ডায়াবেটিস নিয়ন্ত্রণে কার্যকরী ভূমিকা রাখে</li>
                    <li>হৃদরোগের ঝুঁকি কমাতে ভূমিকা রাখে</li>
                    <li>শরীরের ইমোনিটি শক্তিশালী করে</li>
                    <li>সারাদিনের কাজের এনার্জি দেয়</li>
                </ul>',
                'right_title' => 'আউশ ধানের লাল চাল',
                'right_content' => '<ul>
                    <li>শরীরের অতিরিক্ত ওজন হ্রাস</li>
                    <li>ডায়াবেটিস নিয়ন্ত্রণে কার্যকরী ভূমিকা রাখে</li>
                    <li>হৃদরোগের ঝুঁকি কমাতে ভূমিকা রাখে</li>
                    <li>শরীরের ইমোনিটি শক্তিশালী করে</li>
                    <li>সারাদিনের কাজের এনার্জি দেয়</li>
                </ul>',
            ]),
            ContentColumn::default([
                'title' => 'আমাদের থেকে কেন লাল চাল নিবেন',
                'content' => '<ul>
                    <li>আমরা প্রথমত ধানটা হাওড় ও চর অঞ্চল থেকে সংগ্রহ করে থাকি</li>
                    <li>তারপরে ধানটাকে সিদ্ধ করি</li>
                    <li>সিদ্ধ করার পরে আমাদের খোলায় শোকাতে দিয়ে থাকি</li>
                    <li>এর পরে ধানটাকে আমরা আমাদের মেলের মাধ্যমে চালে রূপান্তর করি</li>
                    <li>কিন্তু আমরা কোনো অটো রাইস মেল বা ম্যাশিন ব্যবহার করি না; ফলে আমাদের চালে ফাইবার নষ্ট হয় না</li>
                </ul>',
                'left_title' => '',
                'left_content' => '',
                'right_title' => '',
                'right_content' => '',
            ]),
        ];
    }
}
