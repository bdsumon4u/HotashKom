@if (data_get($sections, 'header.enabled', true))
    <header class="bg-white shadow-sm">
        <div class="flex items-center justify-between px-2 py-4 mx-auto md:px-4 max-w-7xl">
            @if (filled(data_get($logo, 'desktop')) || filled(data_get($logo, 'dashboard')) || filled(data_get($logo, 'login')))
                <img src="{{ asset(data_get($logo, 'desktop') ?? (data_get($logo, 'dashboard') ?? data_get($logo, 'login'))) }}"
                    alt="Company Logo" class="object-contain h-12 max-w-[180px]">
            @else
                <div class="text-2xl font-black tracking-tight text-green-800">
                    {{ config('app.name', 'Hotash CLOTHING') }}
                </div>
            @endif
            <a href="#order"
                class="px-6 py-2 text-sm font-bold text-white transition bg-green-700 rounded-md hover:bg-green-800">
                অর্ডার করুন
            </a>
        </div>
    </header>
@endif
