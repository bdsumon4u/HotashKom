@component('mail::message')
# Hello {{ $order->name }},

Your order has been successfully placed.

@component('mail::button', ['url' => route('track-order', ['order' => $order->id, 'phone' => $order->phone])])
View Order #{{ $order->id }}
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
