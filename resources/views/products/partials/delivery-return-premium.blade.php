@php
$delivery_text = $product->delivery_text ?? setting('delivery_text') ?? '';
@endphp
<style>
.nm-policy-shell {
    background: #fff;
    border: 1px solid #e9ecef;
    border-radius: 14px;
    overflow: hidden;
}

.nm-policy-summary {
    padding: 18px;
}

.nm-policy-return {
    padding-top: 2px;
}

.nm-policy-return-title {
    color: #17221c;
    font-size: 15px;
    font-weight: 700;
    margin: 0 0 5px;
}

.nm-policy-return p {
    color: #59645e;
    font-size: 13px;
    line-height: 1.65;
    margin-bottom: 5px;
}

.nm-policy-links {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 12px;
}

.nm-policy-links a {
    display: inline-flex;
    align-items: center;
    border: 1px solid var(--brand);
    border-radius: 8px;
    color: var(--brand-dark);
    font-size: 13px;
    font-weight: 600;
    padding: 7px 10px;
    text-decoration: none;
}

.nm-policy-links a:hover {
    background: var(--brand);
    color: #fff;
    text-decoration: none;
}

.nm-policy-details {
    border-top: 1px solid #edf0ee;
}

.nm-policy-details summary {
    cursor: pointer;
    list-style: none;
    padding: 13px 18px;
    color: #26372e;
    font-size: 13px;
    font-weight: 700;
    background: #fbfcfb;
}

.nm-policy-details summary::-webkit-details-marker {
    display: none;
}

.nm-policy-details summary::after {
    content: "+";
    float: right;
    color: #1dbf73;
    font-size: 18px;
    line-height: 16px;
}

.nm-policy-details[open] summary::after {
    content: "−";
}

.nm-policy-detail-body {
    padding: 5px 18px 16px;
    color: #4d5952;
    font-size: 13px;
    line-height: 1.65;
}

.nm-policy-detail-body h1,
.nm-policy-detail-body h2,
.nm-policy-detail-body h3,
.nm-policy-detail-body h4,
.nm-policy-detail-body h5,
.nm-policy-detail-body h6 {
    color: #202c25;
    font-size: 15px;
    font-weight: 700;
    margin: 14px 0 7px;
}

.nm-policy-detail-body ul,
.nm-policy-detail-body ol {
    padding-left: 20px;
    margin-bottom: 8px;
}

.nm-policy-detail-body li {
    margin-bottom: 4px;
}

@media (max-width: 767px) {
    .nm-policy-summary {
        padding: 14px;
    }

    .nm-policy-details summary {
        padding-left: 14px;
        padding-right: 14px;
    }

    .nm-policy-detail-body {
        padding-left: 14px;
        padding-right: 14px;
    }
}
</style>

<div class="nm-policy-shell">
    <div class="nm-policy-summary">
        <div class="nm-policy-return">
            <div class="nm-policy-return-title">
                সমস্যা হলে কী করবেন?
            </div>

            <p>
                ভুল পণ্য, delivery damage বা missing item থাকলে
                পণ্য পাওয়ার ৩ দিনের মধ্যে আমাদের সঙ্গে যোগাযোগ করুন।
            </p>

            <p>
                Original box, packaging এবং included accessories
                সংরক্ষণ করুন। পণ্যভেদে আলাদা return condition
                প্রযোজ্য হতে পারে।
            </p>
        </div>

        <div class="nm-policy-links">
            <a href="{{ url('/shipping-and-delivery-policy') }}">
                Shipping Policy
            </a>

            <a href="{{ url('/return-and-refund-policy') }}">
                Return & Refund Policy
            </a>
        </div>
    </div>

    @if ($delivery_text !== '')
        <details class="nm-policy-details">
            <summary>এই পণ্যের Delivery & Return Policy</summary>

            <div class="nm-policy-detail-body">
                {{-- CLOUDFLARE_EMAIL_OFF_DELIVERY --}}
                <!--email_off-->
                {!! $delivery_text !!}
                <!--/email_off-->
            </div>
        </details>
    @endif
</div>
