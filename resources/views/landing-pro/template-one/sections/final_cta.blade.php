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
                    class="px-8 py-3 text-sm font-black uppercase transition bg-red-600 rounded-md hover:bg-red-700">এখনই
                    অর্ডার করুন</a>
                <a href="#order"
                    class="px-8 py-3 text-sm font-black text-green-900 uppercase transition bg-green-300 rounded-md hover:bg-green-200">অর্ডার
                    লিস্ট দেখুন</a>
            </div>
        </div>
    </section>
@endif
