@if (data_get($sections, 'faq.enabled', true) && data_get($sections, 'cta_after_faq.enabled', true))
    <section class="flex justify-center py-5 bg-white border-b md:py-10">
        <div class="max-w-5xl p-3 mx-2 border border-gray-200 rounded-md md:p-6 bg-gray-50">
            <div class="flex flex-col items-center justify-between gap-5 md:flex-row">
                <div class="text-center md:text-left">
                    @php
                        $faqTitle = data_get($sections, 'cta_after_faq.title') ?: 'আর প্রশ্ন নয়, অর্ডার দিন';
                        $faqSubtitle = data_get($sections, 'cta_after_faq.subtitle') ?: '';
                    @endphp
                    <p class="text-lg font-black text-gray-900 md:text-2xl">
                        {{ $faqTitle }}</p>
                    @if (filled($faqSubtitle))
                        <div class="mt-1 text-sm text-gray-600 md:text-base">{!! $faqSubtitle !!}</div>
                    @endif
                </div>
                <div class="flex flex-wrap items-center justify-center gap-3">
                    <a href="#order"
                        class="inline-flex items-center justify-center px-5 py-3 text-sm md:text-base font-black text-white uppercase transition bg-red-600 rounded-md shadow hover:bg-red-700">
                        <i class="mr-2 fas fa-shopping-cart"></i>
                        এখনই অর্ডার
                    </a>
                    @if ($callUrl !== '#order')
                        <a href="{{ $callUrl }}"
                            class="inline-flex items-center justify-center px-5 py-3 text-sm md:text-base font-black text-gray-900 uppercase transition bg-white border border-gray-300 rounded-md shadow hover:bg-gray-100">
                            <i class="mr-2 fas fa-phone-alt"></i>
                            কল করুন
                        </a>
                    @endif
                    @if ($whatsappUrl !== '#order')
                        <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer"
                            class="inline-flex items-center justify-center px-5 py-3 text-sm md:text-base font-black text-white uppercase transition bg-[#25D366] rounded-md shadow hover:bg-[#20ba5a]">
                            <i class="mr-2 fab fa-whatsapp text-lg"></i>
                            হোয়াটসঅ্যাপ
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endif
