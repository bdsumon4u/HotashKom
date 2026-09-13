@php
$delivery_text = $product->delivery_text ?? setting('delivery_text') ?? '';
@endphp
<style>
.bb-policy-shell {
    background: #fff;
    border: 1px solid #e9ecef;
    border-radius: 14px;
    overflow: hidden;
}

.bb-policy-summary {
    padding: 18px;
}

.bb-policy-return {
    padding-top: 2px;
}

.bb-policy-return-title {
    color: #17221c;
    font-size: 15px;
    font-weight: 700;
    margin: 0 0 5px;
}

.bb-policy-return p {
    color: #59645e;
    font-size: 13px;
    line-height: 1.65;
    margin-bottom: 5px;
}

.bb-policy-links {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 12px;
}

.bb-policy-links a {
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

.bb-policy-links a:hover {
    background: var(--brand);
    color: #fff;
    text-decoration: none;
}

.bb-policy-details {
    border-top: 1px solid #edf0ee;
}

.bb-policy-details summary {
    cursor: pointer;
    list-style: none;
    padding: 13px 18px;
    color: #26372e;
    font-size: 13px;
    font-weight: 700;
    background: #fbfcfb;
}

.bb-policy-details summary::-webkit-details-marker {
    display: none;
}

.bb-policy-details summary::after {
    content: "+";
    float: right;
    color: #1dbf73;
    font-size: 18px;
    line-height: 16px;
}

.bb-policy-details[open] summary::after {
    content: "−";
}

.bb-policy-detail-body {
    padding: 5px 18px 16px;
    color: #4d5952;
    font-size: 13px;
    line-height: 1.65;
}

.bb-policy-detail-body h1,
.bb-policy-detail-body h2,
.bb-policy-detail-body h3,
.bb-policy-detail-body h4,
.bb-policy-detail-body h5,
.bb-policy-detail-body h6 {
    color: #202c25;
    font-size: 15px;
    font-weight: 700;
    margin: 14px 0 7px;
}

.bb-policy-detail-body ul,
.bb-policy-detail-body ol {
    padding-left: 20px;
    margin-bottom: 8px;
}

.bb-policy-detail-body li {
    margin-bottom: 4px;
}

@media (max-width: 767px) {
    .bb-policy-summary {
        padding: 14px;
    }

    .bb-policy-details summary {
        padding-left: 14px;
        padding-right: 14px;
    }

    .bb-policy-detail-body {
        padding-left: 14px;
        padding-right: 14px;
    }
}
</style>

<div class="bb-policy-shell">
    <div class="bb-policy-summary">
        <div class="bb-policy-return">
            <div class="bb-policy-return-title">
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

        <div class="bb-policy-links">
            <a href="{{ url('/shipping-and-delivery-policy') }}">
                Shipping Policy
            </a>

            <a href="{{ url('/return-and-refund-policy') }}">
                Return & Refund Policy
            </a>
        </div>
    </div>

    @if ($delivery_text !== '')
        <details class="bb-policy-details">
            <summary>এই পণ্যের Delivery & Return Policy</summary>

            <div class="bb-policy-detail-body">
                {{-- CLOUDFLARE_EMAIL_OFF_DELIVERY --}}
                <!--email_off-->
                {!! $delivery_text !!}
                <!--/email_off-->
            </div>
        </details>
    @endif
</div>
