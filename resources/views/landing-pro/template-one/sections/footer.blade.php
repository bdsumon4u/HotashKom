@if (data_get($sections, 'footer.enabled', true))
    <footer class="py-5 text-center text-white bg-gray-900 md:py-10">
        <div class="max-w-4xl px-2 mx-auto md:px-4">
            @if (filled(data_get($logo, 'desktop')) || filled(data_get($logo, 'dashboard')) || filled(data_get($logo, 'login')))
                <img src="{{ asset(data_get($logo, 'desktop') ?? (data_get($logo, 'dashboard') ?? data_get($logo, 'login'))) }}"
                    alt="Company Logo" class="object-contain mx-auto max-h-20">
            @else
                <div class="text-3xl font-black text-green-500">
                    {{ data_get($sections, 'footer.title', config('app.name', 'Hotash CLOTHING')) }}</div>
            @endif
            <div class="flex justify-center gap-8 mt-5 text-3xl">
                <a href="{{ $facebookUrl }}" target="_blank" rel="noopener noreferrer"
                    class="transition hover:text-blue-500"><i class="fab fa-facebook"></i></a>
                <a href="{{ $whatsappUrl }}" class="transition hover:text-green-500"><i
                        class="fab fa-whatsapp"></i></a>
            </div>
            <p class="max-w-2xl pt-5 mx-auto mt-6 text-sm leading-relaxed text-gray-400 border-t border-gray-800">
                {!! data_get(
                    $sections,
                    'footer.subtitle',
                    data_get(
                        $sections,
                        'footer.description',
                        'আমাদের প্রতিটি পণ্য এক্সপোর্ট কোয়ালিটি সম্পন্ন। Premium Trousers for Premium Customers.',
                    ),
                ) !!}
            </p>
        </div>
    </footer>
@endif
