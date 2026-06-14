<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LandingPagePro extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'template_key',
        'is_published',
        'seo',
        'section_settings',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'seo' => 'array',
            'section_settings' => 'array',
            'published_at' => 'datetime',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(LandingPageProItem::class)->orderBy('sort_order');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public static function sectionLabels(): array
    {
        return [
            'announcement_bar' => 'Announcement Bar',
            'hero' => 'Hero',
            'gallery' => 'Gallery',
            'video' => 'Video',
            'features' => 'Features',
            'size_guide' => 'Size Guide',
            'faq' => 'FAQ',
            'order_form' => 'Order Form',
            'reviews' => 'Reviews',
            'final_cta' => 'Final CTA',
            'footer' => 'Footer',
        ];
    }

    public static function reorderableSectionLabels(): array
    {
        return [
            'hero' => 'Hero',
            'gallery' => 'Gallery',
            'video' => 'Video',
            'features' => 'Features',
            'size_guide' => 'Size Guide',
            'faq' => 'FAQ',
            'order_form' => 'Order Form',
            'reviews' => 'Reviews',
            'final_cta' => 'Final CTA',
        ];
    }

    public static function getRichTextSections(): array
    {
        return ['hero', 'video', 'final_cta'];
    }

    public static function getSectionsWithoutSubtitle(): array
    {
        return ['gallery', 'video', 'features', 'size_guide', 'faq', 'reviews'];
    }

    public static function getCreateSectionDefaults(): array
    {
        return [
            'announcement_bar' => [
                'title' => 'Limited Time Offer! Free Shipping on Orders Over 3 Pieces.',
            ],
            'hero' => [
                'title' => 'Original Exported Guess Trouser',
                'subtitle' => '100% China Dobbi Fabric | Soft & Comfortable',
            ],
            'gallery' => [
                'title' => 'পণ্যটির আরও কিছু ছবি',
            ],
            'cta_after_gallery' => [
                'title' => 'ছবি দেখলেন, এখন অর্ডার করুন',
                'subtitle' => 'স্টক সীমিত, অফার শেষ হওয়ার আগে অর্ডার করুন',
            ],
            'video' => [
                'title' => 'ভিডিওতে বিস্তারিত দেখুন',
                'url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
            ],
            'cta_after_video' => [
                'title' => 'ভিডিও দেখলেন, এখন অর্ডার করুন',
                'subtitle' => 'স্টক সীমিত, অফার শেষ হওয়ার আগে অর্ডার করুন',
            ],
            'features' => [
                'title' => 'কেন আমাদের ট্রাউজার সেরা?',
                'items_text' => implode(PHP_EOL, [
                    'অরিজিনাল চায়না ডবি ফেব্রিক (১০০%)',
                    'এক্সপোর্ট কোয়ালিটি ফিনিশিং এবং স্টিচিং',
                    'প্রিমিয়াম ফিটিং ও এশিয়ান সাইজ চার্ট',
                    'চেইন সহ গভীর পকেট মোবাইল রাখার জন্য নিরাপদ',
                    'অত্যন্ত আরামদায়ক এবং দীর্ঘস্থায়ী ফেব্রিক',
                ]),
            ],
            'size_guide' => [
                'title' => 'সাইজ গাইড (Size Chart)',
                'rows_text' => implode(PHP_EOL, [
                    'L|30-32|38',
                    'XL|32-34|39',
                    '2X|34-36|40',
                    '3XL|36-38|41',
                    '4XL|38-42|42',
                ]),
            ],
            'cta_after_size_guide' => [
                'title' => 'সাইজ মিলেছে? এখন অর্ডার করুন',
                'subtitle' => '',
            ],
            'faq' => [
                'title' => 'সাধারণ জিজ্ঞাসা',
                'items_text' => implode(PHP_EOL, [
                    'ফেব্রিক কি ধোয়ার পর কালার নষ্ট হবে?|জি না, আমরা ১০০% চায়না ডবি প্রিমিয়াম কাপড় ব্যবহার করি যার কালার গ্যারান্টি আছে।',
                    'ঢাকার বাইরে হোম ডেলিভারি পাওয়া যাবে?|জি অবশ্যই, আমরা সারা বাংলাদেশে কুরিয়ারের মাধ্যমে হোম ডেলিভারি দিয়ে থাকি।',
                    'আমি কি ট্রাউজারটি ট্রায়াল দিয়ে নিতে পারবো?|ডেলিভারি ম্যান থাকাকালীন আপনি চেক করে নিতে পারবেন, কোনো সমস্যা থাকলে সাথে সাথেই রিটার্ন করতে পারবেন।',
                    'এটির কোমর কি ইলাস্টিক?|জি, এটিতে হাই-কোয়ালিটি ইলাস্টিক এবং অ্যাডজাস্টেবল ফিতা রয়েছে যা আপনাকে দিবে সর্বোচ্চ আরাম।',
                ]),
            ],
            'cta_after_faq' => [
                'title' => 'আর প্রশ্ন নয়, অর্ডার দিন',
                'subtitle' => '',
            ],
            'order_form' => [
                'title' => 'পণ্য ও পরিমাণ নির্বাচন করুন',
                'subtitle' => 'Choose Products & Quantity',
            ],
            'reviews' => [
                'title' => 'আমাদের কাস্টমারদের মতামত',
                'items_text' => implode(PHP_EOL, [
                    '1 আসাদুল্লাহ বিন সাইদ|খুবই আরামদায়ক ট্রাউজার। প্রিমিয়াম কোয়ালিটি এক্সপোর্ট কাপড়। রেকমেন্ডেড!',
                    '2 মাশরুর আহমেদ|কোয়ালিটি অনেক ভালো। কালার একদম ছবির মতোই। ডেলিভারিও খুব দ্রুত পেয়েছি।',
                    '3 তানজিল ইসলাম|চায়না ডবি ফেব্রিক টা সত্যিই অনেক সফট। এই বাজেটে সেরা ট্রাউজার।',
                    '4 সাইফুল ইসলাম|२টি অর্ডার করেছিলাম, সাইজ এবং ফিটিং একদম পারফেক্ট হয়েছে। ধন্যবাদ!',
                    '5 রাকিবুল ইসলাম|প্রিমিয়াম ফিনিশিং এবং স্টিচিং দেখে আমি সত্যিই অবাক হয়েছি। এই দামেই এত ভালো কোয়ালিটি পাওয়া যায়, ভাবতাম না!',
                    '6 মেহেদী হাসান|ডেলিভারি ম্যান এসে ট্রাউজার চেক করার সুযোগ দেয়ায় আমি খুবই খুশি। কোনো সমস্যা ছিল না, তাই সাথে সাথেই কনফার্ম করে দিয়েছি।',
                ]),
            ],
            'final_cta' => [
                'title' => 'রিভিউ দেখলেন, এবার অর্ডার কনফার্ম করুন',
                'subtitle' => 'আপনার পছন্দের কালার ও সাইজ বেছে এখনই অর্ডার দিন',
            ],
        ];
    }

    public static function ctaSectionKeys(): array
    {
        return [
            'cta_after_gallery',
            'cta_after_video',
            'cta_after_size_guide',
            'cta_after_faq',
        ];
    }

    public static function defaultSectionSettings(): array
    {
        $settings = collect(array_merge(array_keys(static::sectionLabels()), static::ctaSectionKeys()))
            ->mapWithKeys(fn (string $section): array => [$section => ['enabled' => true]])
            ->all();

        $settings['section_order'] = array_keys(static::reorderableSectionLabels());

        return $settings;
    }

    public function mergedSectionSettings(): array
    {
        $stored = $this->section_settings ?? [];

        return array_replace_recursive(static::defaultSectionSettings(), is_array($stored) ? $stored : []);
    }
}
