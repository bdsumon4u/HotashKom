@if (data_get($sections, 'announcement_bar.enabled', true))
    <div class="sticky top-0 z-50 p-2 text-sm font-semibold text-center text-white bg-red-600">
        {{ data_get($sections, 'announcement_bar.title', 'সীমিত সময়ের অফার! ৩ পিসের বেশি অর্ডারে ফ্রি শিপিং।') }}
    </div>
@endif
