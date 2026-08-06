@if (data_get($sections, 'size_guide.enabled', true) && data_get($sections, 'cta_after_size_guide.enabled', true))
    <section class="flex justify-center py-5 bg-white border-b md:py-10">
        <div class="max-w-5xl p-3 mx-2 border-2 border-red-200 rounded-md md:p-6 bg-red-50">
            <div class="flex flex-col items-center justify-between gap-4 md:flex-row">
                <div class="text-center md:text-left">
                    @php
                        $sizeGuideTitle = data_get($sections, 'cta_after_size_guide.title') ?: 'সাইজ মিলেছে? এখন অর্ডার করুন';
                        $sizeGuideSubtitle = data_get($sections, 'cta_after_size_guide.subtitle') ?: '';
                    @endphp
                    <p class="text-lg font-black text-red-700 md:text-2xl">
                        {{ $sizeGuideTitle }}</p>
                    @if (filled($sizeGuideSubtitle))
                        <div class="mt-1 text-sm text-red-600 md:text-base">{!! $sizeGuideSubtitle !!}</div>
                    @endif
                </div>
                <div class="flex flex-wrap items-center justify-center gap-3">
                    <a href="#order"
                        class="inline-flex items-center justify-center px-5 py-3 text-sm md:text-base font-black text-white uppercase transition bg-red-600 rounded-md shadow hover:bg-red-700">
                        <i class="mr-2 fas fa-shopping-cart"></i>
                        অর্ডার সম্পন্ন করুন
                    </a>
                    @if ($callUrl !== '#order')
                        <a href="{{ $callUrl }}"
                            class="inline-flex items-center justify-center px-5 py-3 text-sm md:text-base font-black text-red-900 uppercase transition bg-white border border-red-200 rounded-md shadow hover:bg-red-100">
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
