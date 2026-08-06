@if (data_get($sections, 'hero.enabled', true))
    <section class="py-5 text-center text-white bg-green-800 md:py-20">
        <div class="max-w-5xl px-2 mx-auto md:px-4">
            <h1 class="text-3xl font-extrabold uppercase md:text-5xl">
                {{ data_get($sections, 'hero.title', $landingPagePro->title) }}
            </h1>
            @if (filled(data_get($sections, 'hero.subtitle')))
                <div class="mt-4 text-lg text-green-100 md:text-2xl">{!! data_get($sections, 'hero.subtitle') !!}</div>
            @endif
            <div class="flex justify-center gap-4 my-4 md:my-8">
                <template x-for="(val, unit) in timer" :key="unit">
                    <div class="min-w-[75px] rounded-md bg-white p-3 text-green-800 shadow-2xl">
                        <span class="text-3xl font-black" x-text="val">00</span>
                        <p class="text-[10px] font-black uppercase tracking-widest" x-text="unit"></p>
                    </div>
                </template>
            </div>
            <div class="flex flex-wrap items-center justify-center gap-3 my-4 md:mt-8">
                <a href="#order"
                    class="inline-flex items-center justify-center px-6 py-3 text-base md:text-lg font-black text-white transition bg-red-600 rounded-md shadow-xl hover:bg-red-700">
                    <i class="mr-2 fas fa-shopping-cart"></i>
                    অর্ডার করতে চাই
                </a>
                @if ($callUrl !== '#order')
                    <a href="{{ $callUrl }}"
                        class="inline-flex items-center justify-center px-6 py-3 text-base md:text-lg font-black text-green-900 transition bg-white rounded-md shadow-xl hover:bg-green-50">
                        <i class="mr-2 fas fa-phone-alt"></i>
                        কল করুন
                    </a>
                @endif
                @if ($whatsappUrl !== '#order')
                    <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer"
                        class="inline-flex items-center justify-center px-6 py-3 text-base md:text-lg font-black text-white transition bg-[#25D366] rounded-md shadow-xl hover:bg-[#20ba5a]">
                        <i class="mr-2 fab fa-whatsapp text-xl"></i>
                        হোয়াটসঅ্যাপ
                    </a>
                @endif
            </div>
        </div>
    </section>
@endif

@if (filled($heroImageUrl))
    <section class="py-5 bg-white border-b md:py-10">
        <div class="max-w-3xl px-2 mx-auto md:px-4">
            <img src="{{ $heroImageUrl }}" alt="Hero image"
                class="object-cover w-full border border-gray-100 rounded-md shadow-lg">
        </div>
    </section>
@endif
