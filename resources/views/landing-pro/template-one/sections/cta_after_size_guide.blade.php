@if (data_get($sections, 'cta_after_size_guide.enabled', true))
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
                <a href="#order"
                    class="px-8 py-3 text-sm font-black text-white uppercase transition bg-green-700 rounded-md hover:bg-green-800">অর্ডার
                    সম্পন্ন করুন</a>
            </div>
        </div>
    </section>
@endif
