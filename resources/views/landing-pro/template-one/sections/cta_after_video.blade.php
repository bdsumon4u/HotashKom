@if (data_get($sections, 'cta_after_video.enabled', true))
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
                <a href="#order"
                    class="px-3 py-3 text-sm font-black tracking-wide text-white transition bg-red-600 rounded-md hover:bg-red-700">অর্ডার
                    করুন</a>
            </div>
        </div>
    </section>
@endif
