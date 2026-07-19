<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotash Clothing | Premium Exported Trousers</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        html {
            font-size: 13px;
        }

        @media (min-width: 768px) {
            html {
                font-size: 15px;
            }
        }

        @media (min-width: 1024px) {
            html {
                font-size: 16px;
            }
        }

        @media (min-width: 1280px) {
            html {
                font-size: 17px;
            }
        }

        [x-cloak] {
            display: none !important;
        }

        body {
            visibility: hidden;
        }
    </style>
    <noscript>
        <style>
            body {
                visibility: visible !important;
            }
        </style>
    </noscript>
    <style>

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .scroll-smooth {
            scroll-behavior: smooth;
        }

        .snap-x {
            scroll-snap-type: x mandatory;
        }

        .snap-start {
            scroll-snap-align: start;
        }

        /* Custom Hotash Red/Green Colors */
        .bbi-green {
            background-color: #1b733d;
        }

        .bbi-red {
            background-color: #e22d2d;
        }

        /* Carousel item sizing - gallery */
        .gallery-item {
            flex-shrink: 0;
            scroll-snap-align: start;
        }

        /* Carousel item sizing - reviews */
        .review-item {
            flex-shrink: 0;
            scroll-snap-align: start;
        }
    </style>
</head>

<body class="font-sans text-gray-900 bg-gray-50" x-data="landingPage()">
    @php
        $company = setting('company');
        $contactPhone = preg_replace('/[^\d]/', '', (string) ($company->phone ?? ($company->whatsapp ?? '')));
        if (strlen($contactPhone) === 11) {
            $contactPhone = '88' . $contactPhone;
        }
        $callUrl = $contactPhone ? 'tel:' . $contactPhone : '#order';
        $whatsappUrl = $contactPhone ? 'https://wa.me/' . $contactPhone : '#order';
    @endphp

    <div class="bg-red-600 text-white text-center py-2 text-sm font-semibold sticky top-0 z-[100]">
        Limited Time Offer! Free Shipping on Orders Over 3 Pieces.
    </div>

    <header class="bg-white shadow-sm sticky top-[36px] z-[90]">
        <div class="container flex items-center justify-between px-4 py-4 mx-auto">
            <div class="text-2xl italic font-black tracking-tighter text-green-800">Hotash CLOTHING</div>
            <a href="#order"
                class="px-6 py-2 text-sm font-bold text-white uppercase transition bg-green-700 rounded-full shadow-md hover:bg-green-800">Order
                Now</a>
        </div>
    </header>

    <section class="bg-[#155e33] text-white py-12 md:py-20 text-center">
        <div class="container px-4 mx-auto">
            <h1 class="mb-4 text-3xl font-extrabold leading-tight uppercase md:text-5xl">Original Exported Guess Trouser
            </h1>
            <p class="mb-6 text-xl italic font-semibold text-green-100 md:text-2xl">100% China Dobbi Fabric | Soft &
                Comfortable</p>

            <div class="flex justify-center gap-4 my-8">
                <template x-for="(val, unit) in timer" :key="unit">
                    <div class="bg-white text-green-800 p-3 rounded-xl min-w-[75px] shadow-2xl">
                        <span class="text-3xl font-black" x-text="val">00</span>
                        <p class="text-[10px] font-black uppercase tracking-widest" x-text="unit"></p>
                    </div>
                </template>
            </div>

            <a href="#order"
                class="inline-block px-12 py-4 text-xl font-black text-white transition bg-red-600 rounded-full shadow-2xl hover:bg-red-700 animate-bounce">অর্ডার
                করতে চাই</a>
        </div>
    </section>

    <section class="py-12 overflow-hidden bg-white border-b">
        <div class="container px-4 mx-auto text-center">
            <h3 class="mb-8 text-2xl italic font-bold text-gray-800">পণ্যটির আরও কিছু ছবি</h3>
            <div class="relative">
                <button type="button" @click="prevGallery()"
                    class="absolute left-0 z-10 items-center justify-center hidden w-10 h-10 -translate-y-1/2 bg-white border border-gray-200 rounded-full shadow-lg top-1/2 hover:bg-green-50 md:-left-2 md:flex">
                    <i class="text-green-700 fas fa-chevron-left"></i>
                </button>
                <button type="button" @click="nextGallery()"
                    class="absolute right-0 z-10 items-center justify-center hidden w-10 h-10 -translate-y-1/2 bg-white border border-gray-200 rounded-full shadow-lg top-1/2 hover:bg-green-50 md:-right-2 md:flex">
                    <i class="text-green-700 fas fa-chevron-right"></i>
                </button>
                <div x-ref="gallery"
                    class="flex gap-3 pb-6 overflow-x-hidden md:gap-4 snap-x no-scrollbar scroll-smooth">
                    <template x-for="(img, i) in galleryImages" :key="i">
                        <div
                            class="overflow-hidden border border-gray-100 shadow-lg gallery-item snap-start rounded-2xl">
                            <img :src="img" class="object-cover w-full h-64 md:h-80 lg:h-96" loading="lazy">
                        </div>
                    </template>
                </div>
                <div class="flex justify-center gap-2 mt-4">
                    <template x-for="(img, i) in galleryImages" :key="'gallery-dot-' + i">
                        <button type="button" @click="goToGallery(i)"
                            class="w-2.5 h-2.5 rounded-full transition-all duration-300"
                            :class="galleryIndex === i ? 'w-8 bg-green-700' : 'bg-gray-300 hover:bg-gray-400'"></button>
                    </template>
                </div>
            </div>
        </div>
    </section>

    <section class="py-10 bg-white border-b">
        <div class="container px-4 mx-auto">
            <div class="max-w-5xl p-6 mx-auto bg-gradient-to-r from-green-700 to-green-800 rounded-3xl md:p-8">
                <div class="flex flex-col items-center justify-between gap-5 md:flex-row">
                    <div class="text-center md:text-left">
                        <p class="text-lg font-black text-white md:text-2xl">ছবি দেখলেন, এখন অর্ডার করুন</p>
                        <p class="mt-1 text-sm text-green-100 md:text-base">স্টক সীমিত, অফার শেষ হওয়ার আগে অর্ডার করুন
                        </p>
                    </div>
                    <div class="flex flex-wrap items-center justify-center gap-2">
                        <a href="#order"
                            class="px-3 py-3 text-sm font-black tracking-wide text-white transition bg-red-600 rounded-full shadow-lg hover:bg-red-700">অর্ডার
                            করুন</a>
                        <a href="{{ $callUrl }}"
                            class="px-3 py-3 text-sm font-black tracking-wide text-green-800 transition bg-white rounded-full shadow-lg hover:bg-green-50">কল
                            করুন</a>
                    </div>
                </div>
            </div>
    </section>

    <section class="py-10 border-b bg-gray-50">
        <div class="container max-w-4xl px-4 mx-auto text-center">
            <h2 class="mb-10 text-3xl italic font-black text-green-900 underline decoration-red-500 underline-offset-8">
                ভিডিওতে বিস্তারিত দেখুন</h2>
            <div class="aspect-video rounded-3xl overflow-hidden shadow-2xl border-[10px] border-white">
                <iframe class="w-full h-full" src="https://www.youtube.com/embed/dQw4w9WgXcQ" frameborder="0"
                    allowfullscreen></iframe>
            </div>
        </div>
    </section>

    <section class="py-10 bg-white border-b">
        <div class="container px-4 mx-auto">
            <div class="max-w-5xl p-6 mx-auto bg-gradient-to-r from-green-700 to-green-800 rounded-3xl md:p-8">
                <div class="flex flex-col items-center justify-between gap-5 md:flex-row">
                    <div class="text-center md:text-left">
                        <p class="text-lg font-black text-white md:text-2xl">ভিডিও দেখলেন, এখন অর্ডার করুন</p>
                        <p class="mt-1 text-sm text-green-100 md:text-base">স্টক সীমিত, অফার শেষ হওয়ার আগে অর্ডার করুন
                        </p>
                    </div>
                    <div class="flex flex-wrap items-center justify-center gap-2">
                        <a href="#order"
                            class="px-3 py-3 text-sm font-black tracking-wide text-white transition bg-red-600 rounded-full shadow-lg hover:bg-red-700">অর্ডার
                            করুন</a>
                        <a href="{{ $callUrl }}"
                            class="px-3 py-3 text-sm font-black tracking-wide text-green-800 transition bg-white rounded-full shadow-lg hover:bg-green-50">কল
                            করুন</a>
                    </div>
                </div>
            </div>
    </section>

    <section class="py-10 bg-white">
        <div class="container max-w-5xl px-4 mx-auto">
            <div class="grid items-center gap-12 md:grid-cols-2">
                <div>
                    <h3 class="inline-block pb-2 mb-8 text-3xl font-black uppercase border-b-4 border-green-700">কেন
                        আমাদের ট্রাউজার সেরা?</h3>
                    <ul class="space-y-6">
                        <template x-for="feature in features">
                            <li class="flex items-center gap-5 text-xl font-bold text-gray-700">
                                <i class="text-3xl text-green-600 fas fa-check-circle"></i>
                                <span x-text="feature"></span>
                            </li>
                        </template>
                    </ul>
                </div>
                <div class="p-6 border shadow-inner bg-gray-50 rounded-3xl">
                    <img src="https://picsum.photos/id/1/600/600" class="w-full shadow-md rounded-2xl"
                        alt="Feature Image">
                </div>
            </div>
        </div>
    </section>

    <section class="py-10 border-green-100 bg-green-50 border-y-2">
        <div class="container max-w-3xl px-4 mx-auto">
            <div class="p-8 bg-white border-2 border-green-700 shadow-xl md:p-12 rounded-3xl">
                <h3 class="mb-8 text-2xl font-black text-center underline uppercase decoration-green-600">সাইজ গাইড
                    (Size Chart)</h3>
                <div class="overflow-hidden border-2 border-gray-100 rounded-2xl">
                    <table class="w-full text-center bg-white">
                        <thead class="text-white bg-green-800">
                            <tr>
                                <th class="p-5 font-black">SIZE</th>
                                <th class="p-5 font-black">WAIST (কোমর)</th>
                                <th class="p-5 font-black">LENGTH (লেন্থ)</th>
                            </tr>
                        </thead>
                        <tbody class="text-lg font-black text-gray-800 divide-y-2">
                            <template x-for="row in sizeData">
                                <tr class="transition hover:bg-green-50">
                                    <td class="p-5 border-r bg-gray-50" x-text="row.size"></td>
                                    <td class="p-5 border-r" x-text="row.waist"></td>
                                    <td class="p-5" x-text="row.length"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <section class="py-10 bg-white border-b">
        <div class="container px-4 mx-auto">
            <div class="max-w-4xl p-6 mx-auto border-2 border-red-200 shadow-sm rounded-3xl bg-red-50 md:p-8">
                <div class="flex flex-col items-center justify-between gap-4 md:flex-row">
                    <div class="text-center md:text-left">
                        <p class="text-lg font-black text-red-700 md:text-2xl">সাইজ মিলেছে? এখন অর্ডার করুন</p>
                        <p class="mt-1 text-sm font-semibold text-gray-700 md:text-base">সঠিক সাইজে দ্রুত ডেলিভারি পেতে
                            এখনই অর্ডার করুন</p>
                    </div>
                    <a href="#order"
                        class="px-8 py-3 text-sm font-black tracking-wide text-white uppercase transition bg-green-700 rounded-full shadow-lg hover:bg-green-800">অর্ডার
                        সম্পন্ন করুন</a>
                </div>
            </div>
        </div>
    </section>

    <section class="py-10 bg-white">
        <div class="container max-w-4xl px-4 mx-auto">
            <h2 class="mb-12 text-3xl italic font-black text-center text-green-900 underline decoration-red-600">সাধারণ
                জিজ্ঞাসা</h2>
            <div class="space-y-3" x-data="{ activeFaq: 0 }">
                <template x-for="(faq, index) in faqs" :key="index">
                    <div class="overflow-hidden transition-all duration-300 bg-white border-2 shadow-sm rounded-2xl"
                        :class="activeFaq === index ? 'border-green-600' : 'border-gray-100'">
                        <button @click="activeFaq = activeFaq === index ? null : index"
                            class="flex items-center justify-between w-full px-6 py-3 font-black text-left bg-white hover:bg-green-50">
                            <span class="text-lg" x-text="faq.q"></span>
                            <i class="text-xl transition-transform duration-300 fas"
                                :class="activeFaq === index ? 'fa-minus text-red-500' : 'fa-plus text-green-600'"></i>
                        </button>
                        <div x-show="activeFaq === index" x-cloak x-collapse
                            class="p-6 text-lg font-medium leading-relaxed text-gray-700 border-t border-gray-100 bg-gray-50">
                            <span x-text="faq.a"></span>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </section>

    <section class="py-10 bg-white border-b">
        <div class="container px-4 mx-auto">
            <div class="max-w-5xl p-6 mx-auto border border-gray-200 shadow-sm rounded-3xl bg-gray-50 md:p-8">
                <div class="flex flex-col items-center justify-between gap-5 md:flex-row">
                    <div class="text-center md:text-left">
                        <p class="text-lg font-black text-gray-900 md:text-2xl">আর প্রশ্ন নয়, অর্ডার দিন</p>
                        <p class="mt-1 text-sm text-gray-600 md:text-base">ফর্ম পূরণ করতে না চাইলে সরাসরি কল করুন</p>
                    </div>
                    <div class="flex flex-wrap items-center justify-center gap-3">
                        <a href="#order"
                            class="px-6 py-3 text-sm font-black tracking-wide text-white transition bg-red-600 rounded-full shadow-lg hover:bg-red-700">এখনই
                            অর্ডার</a>
                        <a href="{{ $callUrl }}"
                            class="px-6 py-3 text-sm font-black tracking-wide text-gray-900 transition bg-white border border-gray-300 rounded-full shadow-lg hover:bg-gray-100">কল
                            করুন</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="order" class="py-20 bg-white border-t-8 border-green-800">
        <div class="container px-4 mx-auto max-w-7xl">

            <div class="grid items-start gap-8 lg:grid-cols-12">
                <div class="space-y-4 lg:col-span-7">
                    <div
                        class="p-4 border border-green-100 shadow-sm rounded-2xl bg-gradient-to-r from-green-50 to-white md:p-5">
                        <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                            <div>
                                <h2 class="text-2xl font-black text-gray-900 md:text-3xl">পণ্য ও পরিমাণ নির্বাচন করুন
                                </h2>
                                <p class="mt-1 text-sm font-semibold text-gray-600 md:text-base">Choose Products &
                                    Quantity</p>
                            </div>
                            <span
                                class="inline-flex items-center self-start px-3 py-1 text-xs font-black tracking-wide text-green-800 uppercase bg-green-100 rounded-full md:self-auto">
                                Step 1 of 2
                            </span>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <template x-for="product in products" :key="product.id">
                            <div :class="product.selected ? 'border-blue-500 bg-blue-50' : 'border-gray-200'"
                                class="flex items-center gap-3 p-3 transition-all bg-white border rounded-lg shadow-sm cursor-pointer group"
                                @click="product.selected = !product.selected">

                                <input type="checkbox" x-model="product.selected"
                                    class="flex-shrink-0 w-4 h-4 rounded cursor-pointer accent-blue-600" @click.stop>

                                <img :src="product.image"
                                    class="flex-shrink-0 object-cover w-16 h-16 transition bg-gray-100 border rounded group-hover:scale-105">

                                <div class="flex-1 min-w-0">
                                    <div class="mb-1 text-sm font-bold text-gray-800 line-clamp-2"
                                        x-text="product.name"></div>

                                    <div class="flex items-center gap-2" @click.stop>
                                        <div
                                            class="flex items-center text-sm bg-white border border-gray-200 rounded h-7">
                                            <button @click="if(product.qty > 1) product.qty--"
                                                class="px-2 hover:bg-gray-100">−</button>
                                            <span class="w-6 text-xs font-bold text-center"
                                                x-text="product.qty"></span>
                                            <button @click="product.qty++" class="px-2 hover:bg-gray-100">+</button>
                                        </div>

                                        <div class="text-sm font-bold text-blue-900 whitespace-nowrap">
                                            <template x-if="product.originalPrice">
                                                <span class="text-xs font-normal text-gray-400 line-through"
                                                    x-text="product.originalPrice + '৳'"></span>
                                            </template>
                                            <span x-text="product.price + '৳'"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="lg:col-span-5">
                    <div class="sticky overflow-hidden bg-white border border-gray-200 shadow-2xl rounded-3xl top-40">
                        <div class="px-6 py-5 text-white bg-gradient-to-r from-green-700 to-green-800 md:px-8">
                            <h3 class="text-2xl font-black text-center">ডেলিভারি তথ্য</h3>
                            <p class="mt-1 text-sm font-semibold text-center text-green-100">দ্রুত ডেলিভারির জন্য সঠিক
                                তথ্য দিন</p>
                        </div>

                        <form class="p-5 space-y-4 md:p-6 bg-gray-50" @submit.prevent="submitOrder">
                            <div class="grid grid-cols-1 gap-3 lg:grid-cols-2">
                                <div class="space-y-1.5">
                                    <label class="text-xs font-bold tracking-wide text-gray-700 uppercase">আপনার
                                        নাম</label>
                                    <input type="text" placeholder="যেমন: মোহাম্মদ রাহাত"
                                        x-model.trim="checkout.name" @blur="checkout.touched.name = true"
                                        class="w-full px-3 py-2.5 text-sm transition bg-white border rounded-lg outline-none"
                                        :class="showNameError ?
                                            'border-red-400 focus:border-red-500 focus:ring-2 focus:ring-red-100' :
                                            'border-gray-200 focus:border-green-600 focus:ring-2 focus:ring-green-100'">
                                    <p class="text-xs font-semibold text-red-600" x-show="showNameError">নাম লিখুন</p>
                                </div>

                                <div class="space-y-1.5">
                                    <label class="text-xs font-bold tracking-wide text-gray-700 uppercase">মোবাইল
                                        নাম্বার</label>
                                    <input type="tel" placeholder="01XXXXXXXXX" x-model.trim="checkout.phone"
                                        @blur="checkout.touched.phone = true"
                                        class="w-full px-3 py-2.5 text-sm transition bg-white border rounded-lg outline-none"
                                        :class="showPhoneError ?
                                            'border-red-400 focus:border-red-500 focus:ring-2 focus:ring-red-100' :
                                            'border-gray-200 focus:border-green-600 focus:ring-2 focus:ring-green-100'">
                                    <p class="text-xs font-semibold text-red-600" x-show="showPhoneError">সঠিক মোবাইল
                                        নাম্বার দিন (01XXXXXXXXX)</p>
                                </div>
                            </div>

                            <div class="p-3 bg-white border border-gray-200 rounded-xl">
                                <p class="mb-2 text-xs font-bold tracking-wide text-gray-700 uppercase">ডেলিভারি এরিয়া
                                </p>
                                <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                                    <label
                                        class="flex items-center justify-between p-2.5 border rounded-lg cursor-pointer"
                                        :class="checkout.deliveryArea === 'inside' ? 'border-green-500 bg-green-50' :
                                            'border-gray-200 bg-white'">
                                        <div class="flex items-center gap-2">
                                            <input type="radio" name="delivery_area" value="inside"
                                                x-model="checkout.deliveryArea" class="accent-green-600">
                                            <span class="text-sm font-semibold text-gray-800">Inside Dhaka</span>
                                        </div>
                                        <span class="text-sm font-black text-green-700">70৳</span>
                                    </label>
                                    <label
                                        class="flex items-center justify-between p-2.5 border rounded-lg cursor-pointer"
                                        :class="checkout.deliveryArea === 'outside' ? 'border-green-500 bg-green-50' :
                                            'border-gray-200 bg-white'">
                                        <div class="flex items-center gap-2">
                                            <input type="radio" name="delivery_area" value="outside"
                                                x-model="checkout.deliveryArea" class="accent-green-600">
                                            <span class="text-sm font-semibold text-gray-800">Outside Dhaka</span>
                                        </div>
                                        <span class="text-sm font-black text-green-700">130৳</span>
                                    </label>
                                </div>
                            </div>

                            <div class="space-y-1.5">
                                <label class="text-xs font-bold tracking-wide text-gray-700 uppercase">সম্পূর্ণ
                                    ঠিকানা</label>
                                <textarea placeholder="এরিয়া, থানা, জেলা সহ পূর্ণ ঠিকানা লিখুন" x-model.trim="checkout.address"
                                    @blur="checkout.touched.address = true"
                                    class="w-full px-3 py-2.5 text-sm transition bg-white border rounded-lg outline-none"
                                    :class="showAddressError ?
                                        'border-red-400 focus:border-red-500 focus:ring-2 focus:ring-red-100' :
                                        'border-gray-200 focus:border-green-600 focus:ring-2 focus:ring-green-100'"
                                    rows="3"></textarea>
                                <p class="text-xs font-semibold text-red-600" x-show="showAddressError">সম্পূর্ণ
                                    ঠিকানা লিখুন</p>
                            </div>

                            <div class="mt-2 space-y-1.5 max-h-32 overflow-y-auto pr-1"
                                x-show="selectedProductsCount">
                                <template x-for="product in products.filter(p => p.selected)"
                                    :key="'summary-' + product.id">
                                    <div
                                        class="flex items-start justify-between gap-2 p-2 border border-gray-100 rounded-lg bg-gray-50">
                                        <div class="min-w-0">
                                            <p class="text-xs font-bold text-gray-800 line-clamp-2"
                                                x-text="`${product.name} x ${product.qty}`"></p>
                                        </div>
                                        <p class="text-xs font-black text-green-700 whitespace-nowrap"
                                            x-text="(product.price * product.qty) + '৳'"></p>
                                    </div>
                                </template>
                            </div>

                            <p class="mt-2 text-xs font-semibold text-red-600" x-show="!selectedProductsCount">
                                অন্তত একটি প্রোডাক্ট সিলেক্ট করুন
                            </p>

                            <div class="p-4 bg-white border-2 border-green-300 border-dashed shadow-inner rounded-xl">
                                <div class="space-y-1.5 mb-3 text-sm font-bold text-gray-800">
                                    <div class="flex items-center justify-between">
                                        <span>সাবটোটাল</span>
                                        <span x-text="totalPrice + '৳'"></span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span>ডেলিভারি চার্জ</span>
                                        <span x-text="deliveryCharge + '৳'"></span>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between pt-2 mb-3 border-t border-gray-200">
                                    <span class="text-lg font-black text-gray-900">সর্বমোট</span>
                                    <span class="text-2xl font-black text-green-700" x-text="grandTotal + '৳'"></span>
                                </div>
                                <button type="submit"
                                    class="w-full py-3 text-base font-black tracking-wide text-white transition transform bg-red-600 shadow-xl rounded-xl hover:bg-red-700 active:scale-95 disabled:bg-gray-400"
                                    :disabled="!isCheckoutValid">
                                    অর্ডার কনফার্ম করুন
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-12 overflow-hidden bg-gray-100">
        <div class="container px-4 mx-auto">
            <h3 class="mb-8 text-2xl font-bold text-center text-gray-900">আমাদের কাস্টমারদের মতামত</h3>
            <div class="relative">
                <button type="button" @click="prevReview()"
                    class="absolute left-0 z-10 flex items-center justify-center hidden w-10 h-10 -translate-y-1/2 bg-white border border-gray-200 rounded-full shadow-lg top-1/2 hover:bg-green-50 md:-left-2 md:flex">
                    <i class="text-green-700 fas fa-chevron-left"></i>
                </button>
                <button type="button" @click="nextReview()"
                    class="absolute right-0 z-10 flex items-center justify-center hidden w-10 h-10 -translate-y-1/2 bg-white border border-gray-200 rounded-full shadow-lg top-1/2 hover:bg-green-50 md:-right-2 md:flex">
                    <i class="text-green-700 fas fa-chevron-right"></i>
                </button>
                <div x-ref="reviewTrack"
                    class="flex gap-3 pb-6 overflow-x-hidden md:gap-4 snap-x no-scrollbar scroll-smooth">
                    <template x-for="r in reviews" :key="r.id">
                        <div class="p-4 bg-white border shadow-sm review-item snap-start md:p-6 rounded-xl">
                            <div class="mb-2 text-yellow-400">
                                <template x-for="star in 5" :key="star"><i
                                        class="fas fa-star"></i></template>
                            </div>
                            <p class="mb-3 text-sm italic leading-relaxed text-gray-600 md:text-base">"<span
                                    x-text="r.text"></span>"</p>
                            <div class="text-sm font-bold text-gray-900 md:text-base">- <span x-text="r.name"></span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
            <div class="flex justify-center gap-2 mt-4">
                <template x-for="(review, i) in reviews" :key="'review-dot-' + i">
                    <button type="button" @click="goToReview(i)"
                        class="w-2.5 h-2.5 rounded-full transition-all duration-300"
                        :class="reviewIndex === i ? 'w-8 bg-green-700' : 'bg-gray-300 hover:bg-gray-400'"></button>
                </template>
            </div>
        </div>
    </section>

    <section class="py-12 bg-white border-t border-b">
        <div class="container px-4 mx-auto">
            <div class="max-w-5xl p-6 mx-auto text-center bg-gray-900 rounded-3xl md:p-10">
                <h3 class="text-2xl font-black text-white md:text-3xl">রিভিউ দেখলেন, এবার অর্ডার কনফার্ম করুন</h3>
                <p class="mt-2 text-sm font-semibold text-gray-300 md:text-base">আপনার পছন্দের কালার ও সাইজ বেছে এখনই
                    অর্ডার দিন</p>
                <div class="flex flex-wrap items-center justify-center gap-3 mt-6">
                    <a href="#order"
                        class="px-8 py-3 text-sm font-black tracking-wide text-white uppercase transition bg-red-600 rounded-full shadow-lg hover:bg-red-700">এখনই
                        অর্ডার করুন</a>
                    <a href="#order"
                        class="px-8 py-3 text-sm font-black tracking-wide text-green-900 uppercase transition bg-green-300 rounded-full shadow-lg hover:bg-green-200">অর্ডার
                        লিস্ট দেখুন</a>
                </div>
            </div>
        </div>
    </section>

    <footer class="py-10 text-center text-white bg-gray-900">
        <div class="container px-4 mx-auto">
            <div class="mb-4 text-4xl italic font-black tracking-tighter text-green-500">Hotash CLOTHING</div>
            <div class="flex justify-center gap-10 mb-6 text-5xl">
                <a href="#" class="transition scale-100 hover:text-blue-500 hover:scale-110"><i
                        class="fab fa-facebook"></i></a>
                <a href="#" class="transition scale-100 hover:text-green-500 hover:scale-110"><i
                        class="fab fa-whatsapp"></i></a>
            </div>
            <p class="max-w-2xl pt-5 mx-auto text-sm leading-loose text-gray-500 border-t border-gray-800">
                আমাদের প্রতিটি পণ্য এক্সপোর্ট কোয়ালিটি সম্পন্ন। <br>
                &copy; 2026 Hotash Clothing. Premium Trousers for Premium Customers.
            </p>
        </div>
    </footer>

    <script>
        function landingPage() {
            return {
                // Timer State
                timer: {
                    hours: '00',
                    minutes: '00',
                    seconds: '00'
                },

                // Carousel Indices
                galleryIndex: 0,
                reviewIndex: 0,

                // Checkout Form State
                checkout: {
                    name: '',
                    phone: '',
                    address: '',
                    deliveryArea: 'inside',
                    touched: {
                        name: false,
                        phone: false,
                        address: false,
                    },
                    submitted: false,
                },

                // Content Data Arrays
                galleryImages: [
                    'https://picsum.photos/id/101/800/1000',
                    'https://picsum.photos/id/102/800/1000',
                    'https://picsum.photos/id/103/800/1000',
                    'https://picsum.photos/id/104/800/1000',
                    'https://picsum.photos/id/106/800/1000',
                    'https://picsum.photos/id/107/800/1000'
                ],

                features: [
                    'অরিজিনাল চায়না ডবি ফেব্রিক (১০০%)',
                    'এক্সপোর্ট কোয়ালিটি ফিনিশিং এবং স্টিচিং',
                    'প্রিমিয়াম ফিটিং ও এশিয়ান সাইজ চার্ট',
                    'চেইন সহ গভীর পকেট মোবাইল রাখার জন্য নিরাপদ',
                    'অত্যন্ত আরামদায়ক এবং দীর্ঘস্থায়ী ফেব্রিক'
                ],

                sizeData: [{
                        size: 'L',
                        waist: '30-32',
                        length: '38'
                    },
                    {
                        size: 'XL',
                        waist: '32-34',
                        length: '39'
                    },
                    {
                        size: '2X',
                        waist: '34-36',
                        length: '40'
                    },
                    {
                        size: '3XL',
                        waist: '36-38',
                        length: '41'
                    },
                    {
                        size: '4XL',
                        waist: '38-42',
                        length: '42'
                    }
                ],

                faqs: [{
                        q: 'ফেব্রিক কি ধোয়ার পর কালার নষ্ট হবে?',
                        a: 'জি না, আমরা ১০০% চায়না ডবি প্রিমিয়াম কাপড় ব্যবহার করি যার কালার গ্যারান্টি আছে।'
                    },
                    {
                        q: 'ঢাকার বাইরে হোম ডেলিভারি পাওয়া যাবে?',
                        a: 'জি অবশ্যই, আমরা সারা বাংলাদেশে কুরিয়ারের মাধ্যমে হোম ডেলিভারি দিয়ে থাকি।'
                    },
                    {
                        q: 'আমি কি ট্রাউজারটি ট্রায়াল দিয়ে নিতে পারবো?',
                        a: 'ডেলিভারি ম্যান থাকাকালীন আপনি চেক করে নিতে পারবেন, কোনো সমস্যা থাকলে সাথে সাথেই রিটার্ন করতে পারবেন।'
                    },
                    {
                        q: 'এটির কোমর কি ইলাস্টিক?',
                        a: 'জি, এটিতে হাই-কোয়ালিটি ইলাস্টিক এবং অ্যাডজাস্টেবল ফিতা রয়েছে যা আপনাকে দিবে সর্বোচ্চ আরাম।'
                    }
                ],

                // UPDATED PRODUCTS (Full 10 Items from Screenshot)
                products: [{
                        id: 1,
                        name: 'Guess G301-Ash',
                        price: 850,
                        image: 'https://picsum.photos/id/20/300/300',
                        selected: true,
                        qty: 1
                    },
                    {
                        id: 2,
                        name: 'Guess G302-Olive',
                        price: 850,
                        image: 'https://picsum.photos/id/21/300/300',
                        selected: false,
                        qty: 1
                    },
                    {
                        id: 3,
                        name: 'Guess G303-Khaki',
                        price: 850,
                        image: 'https://picsum.photos/id/22/300/300',
                        selected: false,
                        qty: 1
                    },
                    {
                        id: 4,
                        name: 'Guess G304-Black',
                        price: 850,
                        image: 'https://picsum.photos/id/23/300/300',
                        selected: false,
                        qty: 1
                    },
                    {
                        id: 5,
                        name: 'Guess 2pis Combo-Ash/Black',
                        price: 1600,
                        originalPrice: 1700,
                        image: 'https://picsum.photos/id/24/300/300',
                        selected: false,
                        qty: 1
                    },
                    {
                        id: 6,
                        name: 'Guess 2pis Combo-Ash/Khaki',
                        price: 1600,
                        originalPrice: 1700,
                        image: 'https://picsum.photos/id/25/300/300',
                        selected: false,
                        qty: 1
                    },
                    {
                        id: 7,
                        name: 'Guess 2pis Combo-Ash/Olive',
                        price: 1600,
                        originalPrice: 1700,
                        image: 'https://picsum.photos/id/26/300/300',
                        selected: false,
                        qty: 1
                    },
                    {
                        id: 8,
                        name: 'Guess 2pis Combo-Khaki/Black',
                        price: 1600,
                        originalPrice: 1700,
                        image: 'https://picsum.photos/id/27/300/300',
                        selected: false,
                        qty: 1
                    },
                    {
                        id: 9,
                        name: 'Guess 2pis Combo-Olive/Black',
                        price: 1600,
                        originalPrice: 1700,
                        image: 'https://picsum.photos/id/28/300/300',
                        selected: false,
                        qty: 1
                    },
                    {
                        id: 10,
                        name: 'Guess 2pis Combo-Olive/Khaki',
                        price: 1600,
                        originalPrice: 1700,
                        image: 'https://picsum.photos/id/29/300/300',
                        selected: false,
                        qty: 1
                    },
                ],

                reviews: [{
                        id: 1,
                        name: '1 আসাদুল্লাহ বিন সাইদ',
                        text: 'খুবই আরামদায়ক ট্রাউজার। প্রিমিয়াম কোয়ালিটি এক্সপোর্ট কাপড়। রেকমেন্ডেড!'
                    },
                    {
                        id: 2,
                        name: '2 মাশরুর আহমেদ',
                        text: 'কোয়ালিটি অনেক ভালো। কালার একদম ছবির মতোই। ডেলিভারিও খুব দ্রুত পেয়েছি।'
                    },
                    {
                        id: 3,
                        name: '3 তানজিল ইসলাম',
                        text: 'চায়না ডবি ফেব্রিক টা সত্যিই অনেক সফট। এই বাজেটে সেরা ট্রাউজার।'
                    },
                    {
                        id: 4,
                        name: '4 সাইফুল ইসলাম',
                        text: '২টি অর্ডার করেছিলাম, সাইজ এবং ফিটিং একদম পারফেক্ট হয়েছে। ধন্যবাদ!'
                    },
                    {
                        id: 5,
                        name: '5 রাকিবুল ইসলাম',
                        text: 'প্রিমিয়াম ফিনিশিং এবং স্টিচিং দেখে আমি সত্যিই অবাক হয়েছি। এই দামেই এত ভালো কোয়ালিটি পাওয়া যায়, ভাবতাম না!'
                    },
                    {
                        id: 6,
                        name: '6 মেহেদী হাসান',
                        text: 'ডেলিভারি ম্যান এসে ট্রাউজার চেক করার সুযোগ দেয়ায় আমি খুবই খুশি। কোনো সমস্যা ছিল না, তাই সাথে সাথেই কনফার্ম করে দিয়েছি।'
                    }
                ],

                // Init Logic
                init() {
                    this.startTimer(new Date().getTime() + 86400000);

                    // Initialize carousel widths
                    this.$nextTick(() => {
                        this.initCarouselWidths();
                        window.addEventListener('resize', () => this.initCarouselWidths());
                    });

                    // Auto-Slide Logic for both carousels every 2 seconds
                    setInterval(() => {
                        this.nextGallery();
                        this.nextReview();
                    }, 2000);
                },

                initCarouselWidths() {
                    // Set gallery widths
                    if (this.$refs.gallery) {
                        const galleryContainer = this.$refs.gallery;
                        const galleryWidth = galleryContainer.clientWidth;
                        const itemsToShow = window.innerWidth >= 1024 ? 3 : (window.innerWidth >= 768 ? 2 : 1);
                        const gap = window.innerWidth >= 768 ? 16 : 12; // md:gap-4 = 16px, gap-3 = 12px
                        const totalGapWidth = gap * (itemsToShow - 1);
                        const itemWidth = (galleryWidth - totalGapWidth) / itemsToShow;

                        Array.from(galleryContainer.children).forEach(item => {
                            item.style.width = itemWidth + 'px';
                            item.style.minWidth = itemWidth + 'px';
                            item.style.flex = `0 0 ${itemWidth}px`;
                        });

                        this.scrollToIndex(galleryContainer, this.galleryIndex, 'auto');
                    }

                    // Set review widths
                    if (this.$refs.reviewTrack) {
                        const reviewContainer = this.$refs.reviewTrack;
                        const reviewWidth = reviewContainer.clientWidth;
                        const itemsToShow = window.innerWidth >= 1024 ? 4 : (window.innerWidth >= 768 ? 2 : 1);
                        const gap = window.innerWidth >= 768 ? 16 : 12;
                        const totalGapWidth = gap * (itemsToShow - 1);
                        const itemWidth = (reviewWidth - totalGapWidth) / itemsToShow;

                        Array.from(reviewContainer.children).forEach(item => {
                            item.style.width = itemWidth + 'px';
                            item.style.minWidth = itemWidth + 'px';
                            item.style.flex = `0 0 ${itemWidth}px`;
                        });

                        this.scrollToIndex(reviewContainer, this.reviewIndex, 'auto');
                    }
                },

                nextReview() {
                    this.reviewIndex = (this.reviewIndex + 1) % this.reviews.length;
                    this.scrollToIndex(this.$refs.reviewTrack, this.reviewIndex);
                },

                prevReview() {
                    this.reviewIndex = (this.reviewIndex - 1 + this.reviews.length) % this.reviews.length;
                    this.scrollToIndex(this.$refs.reviewTrack, this.reviewIndex);
                },

                goToReview(index) {
                    this.reviewIndex = index;
                    this.scrollToIndex(this.$refs.reviewTrack, this.reviewIndex);
                },

                nextGallery() {
                    this.galleryIndex = (this.galleryIndex + 1) % this.galleryImages.length;
                    this.scrollToIndex(this.$refs.gallery, this.galleryIndex);
                },

                prevGallery() {
                    this.galleryIndex = (this.galleryIndex - 1 + this.galleryImages.length) % this.galleryImages.length;
                    this.scrollToIndex(this.$refs.gallery, this.galleryIndex);
                },

                goToGallery(index) {
                    this.galleryIndex = index;
                    this.scrollToIndex(this.$refs.gallery, this.galleryIndex);
                },

                scrollToIndex(container, index, behavior = 'smooth') {
                    if (!container || !container.children[index]) return;

                    const target = container.children[index];
                    container.scrollTo({
                        left: target.offsetLeft,
                        behavior,
                    });
                },

                startTimer(expiry) {
                    setInterval(() => {
                        const diff = expiry - new Date().getTime();
                        if (diff < 0) return;
                        this.timer.hours = String(Math.floor((diff / (1000 * 60 * 60)) % 24)).padStart(2, '0');
                        this.timer.minutes = String(Math.floor((diff / 1000 / 60) % 60)).padStart(2, '0');
                        this.timer.seconds = String(Math.floor((diff / 1000) % 60)).padStart(2, '0');
                    }, 1000);
                },

                submitOrder() {
                    this.checkout.submitted = true;
                    this.checkout.touched.name = true;
                    this.checkout.touched.phone = true;
                    this.checkout.touched.address = true;

                    if (!this.isCheckoutValid) {
                        return;
                    }

                    alert('অর্ডার অনুরোধ গ্রহণ করা হয়েছে। শীঘ্রই আমরা যোগাযোগ করব।');
                },

                get selectedProductsCount() {
                    return this.products.filter(p => p.selected).length;
                },

                get deliveryCharge() {
                    return this.checkout.deliveryArea === 'outside' ? 130 : 70;
                },

                get grandTotal() {
                    return this.totalPrice + this.deliveryCharge;
                },

                get showNameError() {
                    const shouldValidate = this.checkout.touched.name || this.checkout.submitted;
                    return shouldValidate && this.checkout.name.length < 2;
                },

                get showPhoneError() {
                    const shouldValidate = this.checkout.touched.phone || this.checkout.submitted;
                    return shouldValidate && !/^01\d{9}$/.test(this.checkout.phone);
                },

                get showAddressError() {
                    const shouldValidate = this.checkout.touched.address || this.checkout.submitted;
                    return shouldValidate && this.checkout.address.length < 10;
                },

                get isCheckoutValid() {
                    return this.selectedProductsCount > 0 && !this.showNameError && !this.showPhoneError && !this
                        .showAddressError;
                },

                get totalPrice() {
                    return this.products.filter(p => p.selected).reduce((sum, p) => sum + (p.price * p.qty), 0);
                }
            }
        }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.body.style.visibility = 'visible';
        });
        if (document.readyState === "interactive" || document.readyState === "complete") {
            document.body.style.visibility = 'visible';
        }
    </script>
</body>

</html>
