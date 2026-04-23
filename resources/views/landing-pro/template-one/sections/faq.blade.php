@if (data_get($sections, 'faq.enabled', true) && !empty($faqs))
    <section class="py-5 bg-white md:py-10">
        <div class="max-w-4xl px-2 mx-auto md:px-4" x-data="{ activeFaq: 0 }">
            <h2 class="mb-8 text-3xl font-black text-center text-green-900">
                {{ data_get($sections, 'faq.title', 'সাধারণ জিজ্ঞাসা') }}</h2>
            <div class="space-y-3">
                @foreach ($faqs as $index => $faq)
                    <div class="overflow-hidden border-2 rounded-md shadow-sm"
                        :class="activeFaq === {{ $index }} ? 'border-green-600' : 'border-gray-100'">
                        <button @click="activeFaq = activeFaq === {{ $index }} ? -1 : {{ $index }}"
                            class="flex items-center justify-between w-full px-6 py-4 font-bold text-left hover:bg-green-50">
                            <span>{{ $faq['q'] }}</span>
                            <i class="fas"
                                :class="activeFaq === {{ $index }} ? 'fa-minus text-red-500' :
                                    'fa-plus text-green-600'"></i>
                        </button>
                        <div x-show="activeFaq === {{ $index }}" x-cloak
                            class="px-6 py-4 text-gray-700 border-t bg-gray-50">
                            {{ $faq['a'] }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif
