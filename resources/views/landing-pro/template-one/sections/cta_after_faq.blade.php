@if (data_get($sections, 'cta_after_faq.enabled', true))
    <section class="flex justify-center py-5 bg-white border-b md:py-10">
        <div class="max-w-5xl p-3 mx-2 border border-gray-200 rounded-md md:p-6 bg-gray-50">
            <div class="flex flex-col items-center justify-between gap-5 md:flex-row">
                <p class="text-lg font-black text-center text-gray-900 md:text-left md:text-2xl">
                    {{ data_get($sections, 'cta_after_faq.title', 'আর প্রশ্ন নয়, অর্ডার দিন') }}</p>
                <div class="flex flex-wrap items-center justify-center gap-3">
                    <a href="#order"
                        class="px-6 py-3 text-sm font-black text-white uppercase transition bg-red-600 rounded-md hover:bg-red-700">এখনই
                        অর্ডার</a>
                    <a href="{{ $callUrl }}"
                        class="px-6 py-3 text-sm font-black text-gray-900 uppercase transition bg-white border border-gray-300 rounded-md hover:bg-gray-100">কল
                        করুন</a>
                </div>
            </div>
        </div>
    </section>
@endif
