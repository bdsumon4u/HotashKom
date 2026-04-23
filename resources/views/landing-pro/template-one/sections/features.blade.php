@if (data_get($sections, 'features.enabled', true))
    @php
        $hasImage = filled(data_get($sections, 'features.image_src'));
    @endphp
    <section class="py-5 bg-white md:py-10">
        <div
            class="grid {{ $hasImage ? 'max-w-6xl' : 'max-w-3xl' }} gap-8 px-2 mx-auto md:px-4 @if ($hasImage) md:grid-cols-2 @endif md:items-center">
            <div>
                <h3 class="mb-6 text-3xl font-black text-green-900">
                    {{ data_get($sections, 'features.title', 'কেন আমাদের ট্রাউজার সেরা?') }}</h3>
                <ul class="space-y-3">
                    @foreach ($features as $feature)
                        <li class="flex items-start gap-3 text-lg font-semibold text-gray-700">
                            <i class="pt-1 text-green-600 fas fa-check-circle"></i>
                            <span>{{ $feature }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
            @if ($hasImage)
                <div>
                    <img src="{{ data_get($sections, 'features.image_src') }}" alt="Feature image"
                        class="object-cover w-full bg-gray-100 border rounded-md shadow-md">
                </div>
            @endif
        </div>
    </section>
@endif
