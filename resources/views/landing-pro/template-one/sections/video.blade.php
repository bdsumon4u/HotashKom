@if (data_get($sections, 'video.enabled', true))
    <section class="py-5 border-b md:py-10 bg-gray-50">
        <div class="max-w-4xl px-2 mx-auto text-center md:px-4">
            <h2 class="mb-8 text-2xl font-black text-green-900">
                {{ data_get($sections, 'video.title', 'ভিডিওতে বিস্তারিত দেখুন') }}</h2>
            <div class="aspect-video overflow-hidden rounded-md border-[10px] border-white shadow-2xl">
                <iframe class="w-full h-full" src="{{ $videoUrl }}" frameborder="0" allowfullscreen></iframe>
            </div>
        </div>
    </section>
@endif
