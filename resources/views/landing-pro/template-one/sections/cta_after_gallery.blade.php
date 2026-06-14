@if (data_get($sections, 'cta_after_gallery.enabled', true))
    <section class="flex justify-center py-5 bg-white md:py-10">
        <div class="max-w-5xl p-3 mx-2 text-white bg-green-800 rounded-md md:p-6">
            <div class="flex flex-col items-center justify-between gap-5 md:flex-row">
                <div class="text-center md:text-left">
                    @php
                        $galleryTitle = data_get($sections, 'cta_after_gallery.title') ?: 'ছবি দেখলেন, এখন অর্ডার করুন';
                        $gallerySubtitle = data_get($sections, 'cta_after_gallery.subtitle') ?: 'স্টক সীমিত, অফার শেষ হওয়ার আগে অর্ডার করুন';
                    @endphp
                    <p class="text-lg font-black md:text-2xl">
                        {{ $galleryTitle }}</p>
                    @if (filled($gallerySubtitle))
                        <div class="mt-1 text-sm text-green-100 md:text-base">{!! $gallerySubtitle !!}</div>
                    @endif
                </div>
                <div class="flex flex-wrap items-center justify-center gap-2">
                    <a href="#order"
                        class="px-3 py-3 text-sm font-black tracking-wide text-white transition bg-red-600 rounded-md hover:bg-red-700">অর্ডার
                        করুন</a>
                    <a href="{{ $callUrl }}"
                        class="px-3 py-3 text-sm font-black tracking-wide text-green-800 transition bg-white rounded-md hover:bg-green-50">কল
                        করুন</a>
                </div>
            </div>
        </div>
    </section>
@endif
