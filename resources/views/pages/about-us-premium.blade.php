@php
    $siteBrand = data_get(setting('company'), 'name') ?: config('app.name');
    $sitePhone = data_get(setting('company'), 'phone') ?: '';
    $siteEmail = data_get(setting('company'), 'email') ?: ('support@' . request()->getHost());
    $siteAddress = data_get(setting('company'), 'address') ?: '';
@endphp

<section class="bb-about-page">
    <!-- Hero Section -->
    <section class="bb-about-hero">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-7 mb-4 mb-lg-0">
                    <div class="bb-about-hero__content">
                        <div class="bb-about-badge">
                            <i class="fas fa-star text-warning mr-2"></i> বিশ্বস্ত প্রিমিয়াম অনলাইন শপিং
                        </div>
                        <h1 class="bb-about-hero__title">
                            প্রয়োজনীয় পছন্দের পণ্য,<br>
                            <span class="bb-about-hero__highlight">নিশ্চিন্ত ও সহজ কেনাকাটা</span>
                        </h1>
                        <p class="bb-about-hero__desc">
                            <strong>{{ $siteBrand }}</strong> আপনার দৈনন্দিন প্রয়োজনীয় ফ্যাশন ও লাইফস্টাইল পণ্য সহজে খুঁজে পাওয়া, অর্ডার করা এবং নিরাপদে ঘরে বসে গ্রহণ করার নির্ভরযোগ্য ঠিকানা।
                        </p>
                        <div class="bb-about-hero__actions">
                            <a href="{{ url('/') }}" class="bb-about-btn bb-about-btn--primary">
                                <i class="fas fa-bag-shopping mr-2"></i> পণ্য দেখুন
                            </a>
                            <a href="{{ url('/contact-us') }}" class="bb-about-btn bb-about-btn--glass">
                                <i class="fas fa-headset mr-2"></i> যোগাযোগ করুন
                            </a>
                        </div>
                        <div class="bb-hero-trust-chips">
                            <div class="bb-trust-chip">
                                <i class="fas fa-circle-check text-success mr-1"></i> ৪.৯/৫ রেটিং
                            </div>
                            <div class="bb-trust-chip">
                                <i class="fas fa-truck-fast text-info mr-1"></i> দ্রুত ডেলিভারি
                            </div>
                            <div class="bb-trust-chip">
                                <i class="fas fa-hand-holding-dollar text-warning mr-1"></i> ক্যাশ অন ডেলিভারি
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="bb-hero-showcase-card">
                        <div class="bb-showcase-header">
                            <div class="bb-showcase-logo-circle">
                                <i class="fas fa-bag-shopping"></i>
                            </div>
                            <div>
                                <div class="bb-showcase-brand">{{ $siteBrand }}</div>
                                <div class="bb-showcase-sub"><i class="fas fa-certificate text-warning mr-1"></i> Verified Official Store</div>
                            </div>
                        </div>
                        <div class="bb-showcase-features">
                            <div class="bb-showcase-item">
                                <div class="bb-showcase-icon"><i class="fas fa-shield-halved"></i></div>
                                <div>
                                    <div class="bb-showcase-item-title">১০০% কোয়ালিটি নিশ্চয়তা</div>
                                    <div class="bb-showcase-item-desc">প্রকৃত ও মানসম্মত পণ্য নির্বাচন</div>
                                </div>
                            </div>
                            <div class="bb-showcase-item">
                                <div class="bb-showcase-icon"><i class="fas fa-bolt"></i></div>
                                <div>
                                    <div class="bb-showcase-item-title">২৪-৪৮ ঘণ্টায় ফাস্ট ডেলিভারি</div>
                                    <div class="bb-showcase-item-desc">সারা বাংলাদেশে দ্রুততম সাপ্লাই</div>
                                </div>
                            </div>
                            <div class="bb-showcase-item">
                                <div class="bb-showcase-icon"><i class="fas fa-boxes-packing"></i></div>
                                <div>
                                    <div class="bb-showcase-item-title">নিরাপদ প্রিমিয়াম প্যাকেজিং</div>
                                    <div class="bb-showcase-item-desc">পণ্যের সম্পূর্ণ সুরক্ষা নিশ্চয়তা</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="container">
        <!-- Trust Cards Grid -->
        <section class="bb-trust-section">
            <div class="bb-trust-grid">
                <div class="bb-trust-card">
                    <div class="bb-trust-icon-box bb-trust-icon--green">
                        <i class="fas fa-certificate"></i>
                    </div>
                    <h3 class="bb-trust-card__title">১০০% কোয়ালিটি পণ্য</h3>
                    <p class="bb-trust-card__desc">পণ্যের সঠিক স্পেসিফিকেশন ও প্রকৃত ছবি পরিষ্কারভাবে উপস্থাপন করি।</p>
                </div>
                <div class="bb-trust-card">
                    <div class="bb-trust-icon-box bb-trust-icon--amber">
                        <i class="fas fa-hand-holding-dollar"></i>
                    </div>
                    <h3 class="bb-trust-card__title">ক্যাশ অন ডেলিভারি</h3>
                    <p class="bb-trust-card__desc">পণ্য হাতে পেয়ে যাচাই করে মূল্য পরিশোধের সুবিধাজনক ব্যবস্থা।</p>
                </div>
                <div class="bb-trust-card">
                    <div class="bb-trust-icon-box bb-trust-icon--indigo">
                        <i class="fas fa-boxes-packing"></i>
                    </div>
                    <h3 class="bb-trust-card__title">নিরাপদ প্যাকেজিং</h3>
                    <p class="bb-trust-card__desc">পরিবহনে পণ্যের শতভাগ সুরক্ষা নিশ্চিত করতে যত্নসহকারে প্যাকেটজাত করা হয়।</p>
                </div>
                <div class="bb-trust-card">
                    <div class="bb-trust-icon-box bb-trust-icon--teal">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h3 class="bb-trust-card__title">দায়িত্বশীল সহায়তা</h3>
                    <p class="bb-trust-card__desc">অর্ডার থেকে ডেলিভারি—যেকোনো তথ্যে আমাদের সাপোর্ট টিম আপনার পাশে।</p>
                </div>
            </div>
        </section>

        <!-- Our Story & Stats Section -->
        <section class="bb-story-section">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <div class="bb-section-tag">
                        <i class="fas fa-award mr-1"></i> আমাদের লক্ষ্য ও পরিচিতি
                    </div>
                    <h2 class="bb-section-heading">
                        স্বচ্ছতা ও বিশ্বস্ততার মেলবন্ধনে <span class="text-brand">{{ $siteBrand }}</span>
                    </h2>
                    <div class="bb-story-text">
                        <p>
                            {{ $siteAddress ? ($siteAddress . ' থেকে পরিচালিত ') : '' }}<strong>{{ $siteBrand }}</strong> বাংলাদেশের সকল প্রান্তের গ্রাহকদের কাছে প্রয়োজনীয়, টেকসই ও আধুনিক ফ্যাশন পণ্য পৌঁছে দিতে অঙ্গীকারবদ্ধ।
                        </p>
                        <p>
                            আমরা বিশ্বাস করি, আদর্শ অনলাইন শপিং মানে কেবল পণ্য বিক্রি নয়; বরং সঠিক তথ্য প্রদান, স্বচ্ছ মূল্য নির্ধারণ, দ্রুত ডেলিভারি এবং বিক্রয়োত্তর আন্তরিক সেবাই একটি দীর্ঘস্থায়ী সম্পর্কের মূল ভিত্তি।
                        </p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="bb-stats-card-group">
                        <div class="bb-stat-card">
                            <div class="bb-stat-icon"><i class="fas fa-users"></i></div>
                            <div class="bb-stat-content">
                                <h4 class="bb-stat-number">১০,০০০+</h4>
                                <span class="bb-stat-label">সন্তুষ্ট গ্রাহকের আস্থা</span>
                            </div>
                        </div>
                        <div class="bb-stat-card">
                            <div class="bb-stat-icon"><i class="fas fa-map-location-dot"></i></div>
                            <div class="bb-stat-content">
                                <h4 class="bb-stat-number">৬৪ জেলা</h4>
                                <span class="bb-stat-label">সমগ্র বাংলাদেশে নির্ভরযোগ্য ডেলিভারি</span>
                            </div>
                        </div>
                        <div class="bb-stat-card">
                            <div class="bb-stat-icon"><i class="fas fa-shield-halved"></i></div>
                            <div class="bb-stat-content">
                                <h4 class="bb-stat-number">১০০%</h4>
                                <span class="bb-stat-label">কোয়ালিটি ও মান নিশ্চয়তা</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Process Section -->
        <section class="bb-process-section">
            <div class="text-center mb-4">
                <div class="bb-section-tag">
                    <i class="fas fa-sliders mr-1"></i> সহজ ও নিরাপদ ধাপ
                </div>
                <h2 class="bb-section-heading">কীভাবে আমরা আপনার অর্ডার প্রস্তুত করি</h2>
                <p class="bb-section-subheading">প্রতিটি ধাপে যত্ন ও সুরক্ষার সাথে আপনার কেনাকাটা সম্পন্ন হয়</p>
            </div>
            <div class="row g-4">
                <div class="col-sm-6 col-lg-3">
                    <div class="bb-process-card">
                        <div class="bb-process-header">
                            <span class="bb-process-step">০১</span>
                            <div class="bb-process-icon"><i class="fas fa-magnifying-glass"></i></div>
                        </div>
                        <h4 class="bb-process-title">পণ্য নির্বাচন</h4>
                        <p class="bb-process-desc">বিবরণ, সাইজ ও প্রকৃত ছবি দেখে আপনার পছন্দের পণ্য সহজে নির্বাচন করুন।</p>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="bb-process-card">
                        <div class="bb-process-header">
                            <span class="bb-process-step">০২</span>
                            <div class="bb-process-icon"><i class="fas fa-clipboard-check"></i></div>
                        </div>
                        <h4 class="bb-process-title">অর্ডার নিশ্চিতকরণ</h4>
                        <p class="bb-process-desc">নাম ও ঠিকানা দিয়ে কয়েক সেকেন্ডেই ঝামেলাহীনভাবে অর্ডার সম্পন্ন করুন।</p>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="bb-process-card">
                        <div class="bb-process-header">
                            <span class="bb-process-step">০৩</span>
                            <div class="bb-process-icon"><i class="fas fa-box-open"></i></div>
                        </div>
                        <h4 class="bb-process-title">কোয়ালিটি চেক ও প্যাকিং</h4>
                        <p class="bb-process-desc">নিখুঁত কোয়ালিটি যাচাইয়ের পর সুরক্ষামূলক প্রিমিয়াম প্যাকেজিং করা হয়।</p>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="bb-process-card">
                        <div class="bb-process-header">
                            <span class="bb-process-step">০৪</span>
                            <div class="bb-process-icon"><i class="fas fa-truck-fast"></i></div>
                        </div>
                        <h4 class="bb-process-title">দ্রুত ডেলিভারি ও গ্রহণ</h4>
                        <p class="bb-process-desc">নির্ভরযোগ্য কুরিয়ারে দ্রুততম সময়ে আপনার দোরগোড়ায় পণ্য পৌঁছে দেওয়া হয়।</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Delivery Section -->
        <section class="bb-delivery-section">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <div class="bb-section-tag">
                        <i class="fas fa-truck mr-1"></i> দেশজুড়ে ডেলিভারি সেবা
                    </div>
                    <h2 class="bb-section-heading">সারা বাংলাদেশে দ্রুত ও নিরাপদ ডেলিভারি</h2>
                    <p class="bb-delivery-desc">
                        আমরা দ্রুততম সময়ে আপনার পণ্য পৌঁছে দিতে বিশ্বস্ত কুরিয়ার পার্টনারদের সাথে কাজ করি। প্রতিটি অর্ডারের সার্বিক অগ্রগতি ট্র্যাকিংয়ের মাধ্যমে নজরে রাখা হয়।
                    </p>
                    <div class="mt-4">
                        <a href="{{ route('track-order') }}" class="bb-about-btn bb-about-btn--outline-brand">
                            <i class="fas fa-location-crosshairs mr-2"></i> অর্ডার ট্র্যাক করুন
                        </a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="bb-delivery-timing-card">
                        <div class="bb-timing-row">
                            <div class="bb-timing-badge"><i class="fas fa-bolt"></i></div>
                            <div class="bb-timing-content">
                                <span class="bb-timing-duration">১–২ কার্যদিবস</span>
                                <span class="bb-timing-zone">ঢাকা সিটি ও পার্শ্ববর্তী এলাকা</span>
                            </div>
                        </div>
                        <div class="bb-timing-row">
                            <div class="bb-timing-badge"><i class="fas fa-truck-fast"></i></div>
                            <div class="bb-timing-content">
                                <span class="bb-timing-duration">২–৪ কার্যদিবস</span>
                                <span class="bb-timing-zone">বিভাগীয় ও জেলা শহরসমূহ</span>
                            </div>
                        </div>
                        <div class="bb-timing-row">
                            <div class="bb-timing-badge"><i class="fas fa-map-pin"></i></div>
                            <div class="bb-timing-content">
                                <span class="bb-timing-duration">৩–৫ কার্যদিবস</span>
                                <span class="bb-timing-zone">উপজেলা ও দুর্গম অঞ্চলসমূহ</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Contact / Support Banner -->
        <section class="bb-about-contact-banner">
            <div class="bb-contact-banner-content">
                <div class="bb-contact-tag">
                    <i class="fas fa-headset mr-1"></i> সার্বক্ষণিক গ্রাহক সেবা
                </div>
                <h2 class="bb-contact-title">যেকোনো প্রশ্ন বা তথ্যের জন্য আমরা প্রস্তুত</h2>
                <p class="bb-contact-desc">
                    পণ্য নির্বাচন, অর্ডার স্ট্যাটাস কিংবা বিক্রয়োত্তর যেকোনো প্রয়োজনে আমাদের বিশেষজ্ঞ টিম আপনাকে সহযোগিতা করবে।
                </p>
                <div class="bb-contact-channels">
                    @if($sitePhone)
                        <a href="tel:{{ $sitePhone }}" class="bb-contact-pill">
                            <i class="fas fa-phone-volume"></i>
                            <span>{{ $sitePhone }}</span>
                        </a>
                    @endif
                    <!--email_off-->
                    <a href="mailto:{{ $siteEmail }}" class="bb-contact-pill">
                        <i class="fas fa-envelope"></i>
                        <span>{{ $siteEmail }}</span>
                    </a>
                    <!--/email_off-->
                </div>
                <div class="mt-4">
                    <a href="{{ url('/') }}" class="bb-about-btn bb-about-btn--white">
                        <i class="fas fa-bag-shopping mr-2"></i> আজই শপিং শুরু করুন
                    </a>
                </div>
            </div>
        </section>
    </div>
</section>

<style>
/* =========================================================
   BagStoreBD Modern Distinctive About Us Page Styles
   ========================================================= */

.bb-about-page {
    color: #1e293b;
    padding-bottom: 70px;
    background: #ffffff;
}

.text-brand {
    color: var(--brand, #059669) !important;
}

/* --- Hero Section --- */
.bb-about-hero {
    background: linear-gradient(135deg, var(--brand-darker, #064e3b) 0%, var(--brand-dark, #059669) 55%, var(--brand, #10b981) 100%);
    color: #ffffff;
    padding: 70px 0 90px;
    position: relative;
    overflow: hidden;
}

.bb-about-hero::before {
    content: "";
    position: absolute;
    top: -50%;
    right: -10%;
    width: 600px;
    height: 600px;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.12) 0%, rgba(255, 255, 255, 0) 70%);
    border-radius: 50%;
    pointer-events: none;
}

.bb-about-hero__content {
    position: relative;
    z-index: 2;
}

.bb-about-badge {
    display: inline-flex;
    align-items: center;
    padding: 6px 16px;
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(8px);
    border: 1px solid rgba(255, 255, 255, 0.25);
    border-radius: 9999px;
    font-size: 13.5px;
    font-weight: 700;
    color: #ffffff;
    margin-bottom: 20px;
}

.bb-about-hero__title {
    font-size: clamp(30px, 4.2vw, 46px);
    line-height: 1.25;
    font-weight: 800;
    letter-spacing: -0.02em;
    color: #ffffff;
    margin: 0 0 16px;
}

.bb-about-hero__highlight {
    color: #a7f3d0;
    font-style: normal;
}

.bb-about-hero__desc {
    font-size: 16px;
    line-height: 1.8;
    color: rgba(255, 255, 255, 0.92);
    margin: 0;
    max-width: 620px;
}

.bb-about-hero__actions {
    display: flex;
    flex-wrap: wrap;
    gap: 14px;
    margin-top: 26px;
}

.bb-hero-trust-chips {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 26px;
}

.bb-trust-chip {
    display: inline-flex;
    align-items: center;
    padding: 5px 12px;
    background: rgba(0, 0, 0, 0.15);
    border: 1px solid rgba(255, 255, 255, 0.18);
    border-radius: 9999px;
    font-size: 12.5px;
    font-weight: 600;
    color: #ffffff;
    backdrop-filter: blur(4px);
}

/* Hero Showcase Card */
.bb-hero-showcase-card {
    background: rgba(255, 255, 255, 0.12);
    backdrop-filter: blur(16px);
    border: 1px solid rgba(255, 255, 255, 0.28);
    border-radius: 18px;
    padding: 24px;
    color: #ffffff;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12);
    position: relative;
    z-index: 2;
}

.bb-showcase-header {
    display: flex;
    align-items: center;
    gap: 14px;
    padding-bottom: 18px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.18);
    margin-bottom: 18px;
}

.bb-showcase-logo-circle {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: #ffffff;
    color: var(--brand-dark, #059669);
    font-size: 22px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.bb-showcase-brand {
    font-size: 18px;
    font-weight: 800;
    color: #ffffff;
}

.bb-showcase-sub {
    font-size: 12px;
    color: #a7f3d0;
    font-weight: 600;
}

.bb-showcase-features {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.bb-showcase-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 14px;
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 10px;
    transition: all 0.2s ease;
}

.bb-showcase-item:hover {
    background: rgba(255, 255, 255, 0.15);
    transform: translateX(4px);
}

.bb-showcase-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: rgba(255, 255, 255, 0.15);
    color: #ffffff;
    font-size: 14px;
    flex-shrink: 0;
}

.bb-showcase-item-title {
    font-size: 13.5px;
    font-weight: 700;
    color: #ffffff;
}

.bb-showcase-item-desc {
    font-size: 11.5px;
    color: rgba(255, 255, 255, 0.75);
}

/* Buttons */
.bb-about-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 48px;
    padding: 11px 24px;
    border-radius: 8px;
    font-size: 14.5px;
    font-weight: 700;
    text-decoration: none !important;
    transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}

.bb-about-btn--primary {
    background: #ffffff;
    color: var(--brand-darker, #064e3b) !important;
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.1);
}

.bb-about-btn--primary:hover {
    transform: translateY(-2px);
    background: #f8fafc;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
}

.bb-about-btn--glass {
    background: rgba(255, 255, 255, 0.12);
    color: #ffffff !important;
    border: 1px solid rgba(255, 255, 255, 0.3);
    backdrop-filter: blur(8px);
}

.bb-about-btn--glass:hover {
    background: rgba(255, 255, 255, 0.22);
    color: #ffffff !important;
    transform: translateY(-2px);
}

.bb-about-btn--white {
    background: #ffffff;
    color: var(--brand-dark, #059669) !important;
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.12);
}

.bb-about-btn--white:hover {
    background: #f8fafc;
    transform: translateY(-2px);
}

.bb-about-btn--outline-brand {
    border: 1.5px solid var(--brand);
    color: var(--brand-dark) !important;
    background: #ffffff;
}

.bb-about-btn--outline-brand:hover {
    background: var(--brand);
    color: #ffffff !important;
    transform: translateY(-2px);
    box-shadow: 0 4px 14px rgba(var(--brand-rgb), 0.25);
}

/* --- Trust Grid Section --- */
.bb-trust-section {
    position: relative;
    z-index: 10;
    margin-top: -46px;
    margin-bottom: 60px;
}

.bb-trust-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
}

.bb-trust-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 24px 20px;
    box-shadow: 0 8px 24px -4px rgba(15, 23, 42, 0.05);
    transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    position: relative;
    overflow: hidden;
}

.bb-trust-card::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--brand), var(--brand-dark));
    opacity: 0;
    transition: opacity 0.25s ease;
}

.bb-trust-card:hover {
    transform: translateY(-4px);
    border-color: var(--brand-border);
    box-shadow: 0 14px 30px -4px rgba(var(--brand-rgb), 0.12);
}

.bb-trust-card:hover::before {
    opacity: 1;
}

.bb-trust-icon-box {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 48px;
    height: 48px;
    border-radius: 12px;
    font-size: 20px;
    margin-bottom: 16px;
    transition: transform 0.2s ease;
}

.bb-trust-card:hover .bb-trust-icon-box {
    transform: scale(1.08);
}

.bb-trust-icon--green {
    background: #ecfdf5;
    color: #059669;
    border: 1px solid #a7f3d0;
}

.bb-trust-icon--amber {
    background: #fffbeb;
    color: #d97706;
    border: 1px solid #fde68a;
}

.bb-trust-icon--indigo {
    background: #eef2ff;
    color: #4f46e5;
    border: 1px solid #c7d2fe;
}

.bb-trust-icon--teal {
    background: #f0fdfa;
    color: #0d9488;
    border: 1px solid #99f6e4;
}

.bb-trust-card__title {
    font-size: 16.5px;
    font-weight: 750;
    color: #0f172a;
    margin: 0 0 8px;
    letter-spacing: -0.01em;
}

.bb-trust-card__desc {
    color: #64748b;
    font-size: 13.5px;
    line-height: 1.65;
    margin: 0;
}

/* --- Section Typography --- */
.bb-section-tag {
    display: inline-flex;
    align-items: center;
    padding: 4px 12px;
    background: var(--brand-soft, #f1f5f9);
    border: 1px solid var(--brand-border, #e2e8f0);
    color: var(--brand-dark, #059669);
    border-radius: 9999px;
    font-size: 12.5px;
    font-weight: 700;
    margin-bottom: 14px;
}

.bb-section-heading {
    font-size: clamp(24px, 3vw, 34px);
    font-weight: 800;
    line-height: 1.3;
    color: #0f172a;
    letter-spacing: -0.02em;
    margin-bottom: 16px;
}

.bb-section-subheading {
    color: #64748b;
    font-size: 15px;
    margin-bottom: 0;
}

/* --- Story Section --- */
.bb-story-section {
    padding: 30px 0 60px;
    border-bottom: 1px solid #f1f5f9;
}

.bb-story-text p {
    color: #475569;
    font-size: 15.5px;
    line-height: 1.8;
    margin-bottom: 14px;
}

.bb-stats-card-group {
    display: flex;
    flex-direction: column;
    gap: 12px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 20px;
}

.bb-stat-card {
    display: flex;
    align-items: center;
    gap: 16px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 16px 18px;
    transition: all 0.2s ease;
}

.bb-stat-card:hover {
    border-color: var(--brand);
    transform: translateX(4px);
}

.bb-stat-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 44px;
    height: 44px;
    border-radius: 10px;
    background: var(--brand-soft);
    color: var(--brand);
    font-size: 18px;
    flex-shrink: 0;
}

.bb-stat-number {
    font-size: 20px;
    font-weight: 800;
    color: #0f172a;
    margin: 0 0 2px;
}

.bb-stat-label {
    color: #64748b;
    font-size: 13px;
    font-weight: 500;
}

/* --- Process Section --- */
.bb-process-section {
    padding: 60px 0;
    border-bottom: 1px solid #f1f5f9;
}

.bb-process-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 24px 20px;
    height: 100%;
    box-shadow: 0 4px 16px -2px rgba(15, 23, 42, 0.03);
    transition: all 0.25s ease;
}

.bb-process-card:hover {
    border-color: var(--brand);
    transform: translateY(-3px);
    box-shadow: 0 10px 24px -2px rgba(15, 23, 42, 0.08);
}

.bb-process-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 16px;
}

.bb-process-step {
    font-size: 13px;
    font-weight: 800;
    color: var(--brand-dark);
    background: var(--brand-soft);
    border: 1px solid var(--brand-border);
    padding: 3px 9px;
    border-radius: 6px;
}

.bb-process-icon {
    font-size: 18px;
    color: #94a3b8;
}

.bb-process-title {
    font-size: 16px;
    font-weight: 750;
    color: #0f172a;
    margin-bottom: 8px;
}

.bb-process-desc {
    color: #64748b;
    font-size: 13.5px;
    line-height: 1.65;
    margin: 0;
}

/* --- Delivery Section --- */
.bb-delivery-section {
    padding: 60px 0;
}

.bb-delivery-desc {
    color: #475569;
    font-size: 15.5px;
    line-height: 1.8;
}

.bb-delivery-timing-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.bb-timing-row {
    display: flex;
    align-items: center;
    gap: 16px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 16px 18px;
    transition: all 0.2s ease;
}

.bb-timing-row:hover {
    border-color: var(--brand);
    transform: translateX(4px);
}

.bb-timing-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 42px;
    height: 42px;
    border-radius: 10px;
    background: var(--brand-soft);
    color: var(--brand);
    font-size: 18px;
    flex-shrink: 0;
}

.bb-timing-duration {
    display: block;
    font-size: 17px;
    font-weight: 800;
    color: #0f172a;
}

.bb-timing-zone {
    font-size: 13px;
    color: #64748b;
}

/* --- Contact Banner --- */
.bb-about-contact-banner {
    background: linear-gradient(135deg, var(--brand-darker, #064e3b) 0%, var(--brand-dark, #059669) 100%);
    border-radius: 16px;
    color: #ffffff;
    text-align: center;
    padding: 56px 24px;
    margin-top: 20px;
    box-shadow: 0 16px 36px -6px rgba(var(--brand-rgb), 0.25);
    position: relative;
    overflow: hidden;
}

.bb-contact-banner-content {
    max-width: 680px;
    margin: 0 auto;
    position: relative;
    z-index: 2;
}

.bb-contact-tag {
    display: inline-flex;
    align-items: center;
    padding: 5px 14px;
    background: rgba(255, 255, 255, 0.15);
    border: 1px solid rgba(255, 255, 255, 0.25);
    border-radius: 9999px;
    font-size: 12.5px;
    font-weight: 700;
    margin-bottom: 16px;
}

.bb-contact-title {
    font-size: clamp(24px, 3vw, 32px);
    font-weight: 800;
    line-height: 1.3;
    color: #ffffff;
    margin-bottom: 12px;
}

.bb-contact-desc {
    color: rgba(255, 255, 255, 0.88);
    font-size: 15px;
    line-height: 1.75;
    margin-bottom: 24px;
}

.bb-contact-channels {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 12px;
}

.bb-contact-pill {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 18px;
    background: rgba(255, 255, 255, 0.12);
    border: 1px solid rgba(255, 255, 255, 0.22);
    border-radius: 8px;
    color: #ffffff !important;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none !important;
    backdrop-filter: blur(6px);
    transition: all 0.2s ease;
}

.bb-contact-pill:hover {
    background: rgba(255, 255, 255, 0.22);
    transform: translateY(-2px);
}

/* --- Mobile Responsiveness --- */
@media (max-width: 991px) {
    .bb-trust-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    .bb-hero-showcase-card {
        margin-top: 24px;
    }
}

@media (max-width: 575px) {
    .bb-about-hero {
        padding: 50px 0 70px;
    }

    .bb-about-hero__desc {
        font-size: 15px;
    }

    .bb-trust-grid {
        grid-template-columns: 1fr;
        gap: 12px;
    }

    .bb-trust-section {
        margin-top: -30px;
        margin-bottom: 40px;
    }

    .bb-about-contact-banner {
        padding: 40px 16px;
    }
}
</style>
