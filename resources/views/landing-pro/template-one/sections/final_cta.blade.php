@if (data_get($sections, 'final_cta.enabled', true))
    <section class="flex justify-center py-5 bg-white border-b md:py-10">
        <div class="max-w-5xl p-3 mx-2 text-center text-white bg-gray-900 rounded-md md:p-6">
            <h3 class="text-2xl font-black">
                {{ data_get($sections, 'final_cta.title', 'রিভিউ দেখলেন, এবার অর্ডার কনফার্ম করুন') }}</h3>
            @if (filled(data_get($sections, 'final_cta.subtitle')))
                <div class="mt-2 text-sm text-gray-300">{!! data_get($sections, 'final_cta.subtitle') !!}</div>
            @endif
            @if (filled(data_get($sections, 'final_cta.image_src')))
                <img src="{{ data_get($sections, 'final_cta.image_src') }}" alt="Final CTA"
                    class="object-cover mx-auto mt-5 border rounded-md max-h-52 border-white/20">
            @endif
            <div class="flex flex-wrap justify-center gap-3 mt-6">
                <a href="#order"
                    class="inline-flex items-center justify-center px-6 py-3 text-sm md:text-base font-black text-white uppercase transition bg-red-600 rounded-md shadow hover:bg-red-700">
                    <i class="mr-2 fas fa-shopping-cart"></i>
                    এখনই অর্ডার করুন
                </a>
                @if ($callUrl !== '#order')
                    <a href="{{ $callUrl }}"
                        class="inline-flex items-center justify-center px-6 py-3 text-sm md:text-base font-black text-gray-900 uppercase transition bg-white rounded-md shadow hover:bg-gray-100">
                        <i class="mr-2 fas fa-phone-alt"></i>
                        কল করুন
                    </a>
                @endif
                @if ($whatsappUrl !== '#order')
                    <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer"
                        class="inline-flex items-center justify-center px-6 py-3 text-sm md:text-base font-black text-white uppercase transition bg-[#25D366] rounded-md shadow hover:bg-[#20ba5a]">
                        <i class="mr-2 fab fa-whatsapp text-lg"></i>
                        হোয়াটসঅ্যাপ
                    </a>
                @endif
            </div>
        </div>
    </section>
@endif
