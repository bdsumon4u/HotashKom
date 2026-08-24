<section class="nm-shop-hero">
    <div class="container">
        <div class="nm-shop-hero__content">
            <span class="nm-shop-hero__badge">{{ ($company->name ?? config('app.name')) }} Online Shop</span>
            <h1>সব পণ্য এক জায়গায়</h1>
            <p>দৈনন্দিন প্রয়োজনের পণ্য সহজে খুঁজুন, পছন্দ করুন এবং নিশ্চিন্তে অর্ডার করুন।</p>
        </div>
    </div>
</section>

<section class="nm-shop-trust">
    <div class="container">
        <div class="nm-shop-trust__grid">
            <div><span>✓</span><strong>ক্যাশ অন ডেলিভারি</strong><small>পণ্য হাতে পেয়ে পেমেন্ট করুন</small></div>
            <div><span>□</span><strong>সারা দেশে ডেলিভারি</strong><small>আপনার ঠিকানায় পণ্য পৌঁছে দেওয়ার চেষ্টা করি</small></div>
            <div><span>◉</span><strong>সহজ অর্ডার</strong><small>কয়েকটি ধাপেই অর্ডার সম্পন্ন করুন</small></div>
        </div>
    </div>
</section>

@push('styles')
<style>
    .nm-shop-hero{background:linear-gradient(135deg,var(--brand-darker) 0%,var(--brand-dark) 50%,var(--brand) 100%);color:#fff;padding:55px 0 62px;position:relative;overflow:hidden}
    .nm-shop-hero:after{content:"";position:absolute;width:310px;height:310px;border:42px solid rgba(255,255,255,.11);border-radius:50%;right:-78px;top:-150px}
    .nm-shop-hero__content{position:relative;z-index:1;max-width:720px}
    .nm-shop-hero__badge{display:inline-block;padding:7px 13px;background:rgba(255,255,255,.14);border:1px solid rgba(255,255,255,.24);border-radius:30px;font-size:13px;font-weight:700;margin-bottom:14px}
    .nm-shop-hero h1{color:#fff;margin:0;font-size:clamp(31px,4vw,46px);font-weight:800;line-height:1.2}
    .nm-shop-hero p{max-width:650px;margin:13px 0 0;color:#effff5;font-size:16px;line-height:1.75}
    .nm-shop-trust{background:var(--brand-soft);padding:0 0 24px}
    .nm-shop-trust__grid{display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-top:-19px;position:relative;z-index:2}
    .nm-shop-trust__grid>div{display:grid;grid-template-columns:35px 1fr;column-gap:10px;align-items:center;padding:17px;background:#fff;border:1px solid var(--brand-border);border-radius:10px;box-shadow:0 8px 20px rgba(14,72,43,.07)}
    .nm-shop-trust__grid span{grid-row:span 2;display:grid;place-items:center;width:34px;height:34px;border-radius:9px;background:var(--brand-soft);color:var(--brand-dark);font-weight:800;font-size:18px}
    .nm-shop-trust__grid strong{color:#193429;font-size:14px}
    .nm-shop-trust__grid small{color:#6b7d72;font-size:12px;line-height:1.45}
    .nm-shop-products-block{background:var(--brand-soft);padding:12px 0 54px}
    .nm-shop-products-block .products-view__options{background:#fff;border:1px solid var(--brand-border);border-radius:10px;padding:14px 17px;margin-bottom:18px}
    .nm-shop-products-block .view-options__legend{color:#315142;font-size:14px;font-weight:700}
    .nm-shop-products-block .filter-sidebar{border:1px solid var(--brand-border)!important;border-radius:12px!important;box-shadow:0 9px 24px rgba(20,62,41,.06)}
    .nm-shop-products-block .filter-sidebar__title,.nm-shop-products-block .filter-block__title{color:#183a29!important;font-weight:750}
    .nm-shop-products-block .filter-checkbox input[type="checkbox"]{accent-color:var(--brand)}
    .nm-shop-products-block .filter-actions .btn-secondary{background:var(--brand-soft)!important;border-color:var(--brand-border)!important;color:#315142!important}
    .nm-shop-products-block .products-list__item .product-card{height:100%;border:1px solid var(--brand-border);border-radius:12px;overflow:hidden;background:#fff;box-shadow:0 4px 13px rgba(20,62,41,.045);transition:transform .2s ease,box-shadow .2s ease}
    .nm-shop-products-block .products-list__item .product-card:hover{transform:translateY(-4px);box-shadow:0 14px 28px rgba(20,62,41,.12)}
    .nm-shop-products-block .product-card__image{background:#fff}
    .nm-shop-products-block .product-card__info{padding:14px 14px 7px}
    .nm-shop-products-block .product-card__name a{color:#1a3327!important;font-weight:700;line-height:1.5}
    .nm-shop-products-block .product-card__actions{padding:0 14px 14px}
    .nm-shop-products-block .product-card__availability{font-size:12px;color:#64756b}
    .nm-shop-products-block .product-card__new-price{color:var(--brand-dark)!important;font-size:18px!important;font-weight:800!important}
    .nm-shop-products-block .product-card__old-price{color:#8a9690!important;font-size:13px}
    .nm-shop-products-block .product-card__badge--sale{background:var(--brand-dark)!important}
    @media(max-width:767px){.nm-shop-hero{padding:43px 0 48px}.nm-shop-trust__grid{grid-template-columns:1fr;margin-top:-15px}.nm-shop-trust{padding-bottom:15px}.nm-shop-products-block{padding-top:8px}}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    function removeDiscountBadges() {
        document.querySelectorAll('.nm-shop-products-block .product-card__badge--sale').forEach(function (badge) {
            if (badge.textContent.trim().toLowerCase() !== 'sold') {
                badge.style.display = 'none';
            }
        });
    }

    removeDiscountBadges();

    new MutationObserver(removeDiscountBadges).observe(document.body, {
        childList: true,
        subtree: true
    });
});
</script>
@endpush
