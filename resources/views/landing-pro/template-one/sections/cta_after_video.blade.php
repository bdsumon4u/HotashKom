@if (data_get($sections, 'video.enabled', true) && data_get($sections, 'cta_after_video.enabled', true))
    <section class="flex justify-center py-5 bg-white border-b md:py-10">
        <div class="max-w-5xl p-3 mx-2 text-white bg-green-800 rounded-md md:p-6">
            <div class="flex flex-col items-center justify-between gap-5 md:flex-row">
                <div class="text-center md:text-left">
                    @php
                        $videoTitle = data_get($sections, 'cta_after_video.title') ?: 'ভিডিও দেখলেন, এখন অর্ডার করুন';
                        $videoSubtitle = data_get($sections, 'cta_after_video.subtitle') ?: 'স্টক সীমিত, অফার শেষ হওয়ার আগে অর্ডার করুন';
                    @endphp
                    <p class="text-lg font-black md:text-2xl">
                        {{ $videoTitle }}</p>
                    @if (filled($videoSubtitle))
                        <div class="mt-1 text-sm text-green-100 md:text-base">{!! $videoSubtitle !!}</div>
                    @endif
                </div>
                <div class="flex flex-wrap items-center justify-center gap-3">
                    <a href="#order"
                        class="inline-flex items-center justify-center px-5 py-3 text-sm md:text-base font-black tracking-wide text-white transition bg-red-600 rounded-md shadow hover:bg-red-700">
                        <i class="mr-2 fas fa-shopping-cart"></i>
                        অর্ডার করুন
                    </a>
                    @if ($callUrl !== '#order')
                        <a href="{{ $callUrl }}"
                            class="inline-flex items-center justify-center px-5 py-3 text-sm md:text-base font-black tracking-wide text-green-900 transition bg-white rounded-md shadow hover:bg-green-50">
                            <i class="mr-2 fas fa-phone-alt"></i>
                            কল করুন
                        </a>
                    @endif
                    @if ($whatsappUrl !== '#order')
                        <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer"
                            class="inline-flex items-center justify-center px-5 py-3 text-sm md:text-base font-black tracking-wide text-white transition bg-[#25D366] rounded-md shadow hover:bg-[#20ba5a]">
                            <i class="mr-2 fab fa-whatsapp text-lg"></i>
                            হোয়াটসঅ্যাপ
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endif
