<section class="bb-shop-showcase">
    <div class="container">
        <div class="bb-shop-showcase__wrapper">
            <div class="row align-items-center">
                <!-- Left Column: Headline, Info & Quick Category Pills -->
                <div class="col-12 col-lg-7 mb-4 mb-lg-0">
                    <div class="bb-shop-showcase__content pr-lg-4">
                        <div class="bb-shop-breadcrumb mb-3 d-flex align-items-center">
                            <a href="{{ url('/') }}" class="text-white-50 d-inline-flex align-items-center" style="font-size: 13px; text-decoration: none;">
                                <i class="fas fa-house mr-1"></i> Home
                            </a>
                            <span class="text-white-50 mx-2" style="font-size: 11px;">/</span>
                            <span class="text-white font-weight-bold" style="font-size: 13px;">Shop All Products</span>
                        </div>

                        <div class="d-inline-flex align-items-center mb-3 px-3 py-1" style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); backdrop-filter: blur(10px); border-radius: 4px;">
                            <span class="badge badge-success px-2 py-1 mr-2 font-weight-bold" style="font-size: 11px; border-radius: 3px;">Official</span>
                            <span class="text-white font-weight-bold" style="font-size: 13px;">{{ ($company->name ?? config('app.name')) }} Storefront</span>
                        </div>

                        <h1 class="bb-shop-title text-white font-weight-bold mb-3" style="font-size: clamp(24px, 3.2vw, 36px); line-height: 1.3; letter-spacing: -0.5px;">
                            প্রিমিয়াম ব্যাগ ও লাইফস্টাইল কালেকশন
                        </h1>

                        <p class="bb-shop-subtitle text-white-50 mb-3" style="font-size: 14.5px; line-height: 1.65; max-width: 580px;">
                            অফিস, ট্রাভেল, কলেজ ও দৈনন্দিন ব্যবহারের জন্য প্রিমিয়াম কোয়ালিটি ব্যাগ। ১০০% ক্যাশ অন ডেলিভারি ও দ্রুত হোম ডেলিভারি সুবিধা।
                        </p>

                        <!-- Quick Category Pills -->
                        <div class="bb-shop-quick-tags d-flex flex-wrap align-items-center" style="gap: 8px; margin-top: 12px;">
                            <span class="text-white-50 small mr-2 font-weight-bold" style="white-space: nowrap;">জনপ্রিয় বিভাগ:</span>
                            @foreach(\App\Models\Category::whereNull('parent_id')->where('is_enabled', true)->take(6)->get() as $cat)
                                <a href="{{ route('category.show', $cat) }}" class="bb-category-pill" wire:navigate.hover>
                                    <span>{{ $cat->name }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Right Column: Elevated Trust & Assurance Card -->
                <div class="col-12 col-lg-5">
                    <div class="bb-shop-assurance-card">
                        <div class="bb-assurance-header d-flex align-items-center justify-content-between pb-3 mb-3" style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                            <div class="d-flex align-items-center">
                                <div class="bb-assurance-icon-sparkle mr-2">
                                    <i class="fas fa-gem"></i>
                                </div>
                                <div>
                                    <h6 class="text-white font-weight-bold mb-0" style="font-size: 14.5px;">{{ ($company->name ?? config('app.name')) }} প্রতিশ্রুতি</h6>
                                    <span class="text-white-50" style="font-size: 11.5px;">নিরাপদ ও নির্ভরযোগ্য শপিং</span>
                                </div>
                            </div>
                            <span class="badge px-2 py-1" style="background: rgba(var(--brand-rgb), 0.2); color: #6ee7b7; border: 1px solid rgba(var(--brand-rgb), 0.4); border-radius: 4px; font-size: 11px; font-weight: 700;">
                                4.9 ★ Rating
                            </span>
                        </div>

                        <div class="bb-assurance-items">
                            <div class="bb-assurance-item d-flex align-items-center mb-3">
                                <div class="bb-assurance-item-icon mr-3" style="background: rgba(16, 185, 129, 0.15); color: #34d399;">
                                    <i class="fas fa-truck-fast"></i>
                                </div>
                                <div class="bb-assurance-item-text">
                                    <strong class="text-white d-block font-weight-bold" style="font-size: 13.5px; line-height: 1.3;">সারা দেশে দ্রুত ডেলিভারি</strong>
                                    <small class="text-white-50 d-block" style="font-size: 11.5px; line-height: 1.4;">ঢাকা সিটিতে ২৪-৪৮ ঘণ্টা, সারা দেশে ৭২ ঘণ্টায়</small>
                                </div>
                            </div>

                            <div class="bb-assurance-item d-flex align-items-center mb-3">
                                <div class="bb-assurance-item-icon mr-3" style="background: rgba(59, 130, 246, 0.15); color: #60a5fa;">
                                    <i class="fas fa-hand-holding-dollar"></i>
                                </div>
                                <div class="bb-assurance-item-text">
                                    <strong class="text-white d-block font-weight-bold" style="font-size: 13.5px; line-height: 1.3;">ক্যাশ অন ডেলিভারি সুবিধা</strong>
                                    <small class="text-white-50 d-block" style="font-size: 11.5px; line-height: 1.4;">পণ্য হাতে পেয়ে চেক করে সম্পূর্ণ মূল্য পরিশোধ করুন</small>
                                </div>
                            </div>

                            <div class="bb-assurance-item d-flex align-items-center">
                                <div class="bb-assurance-item-icon mr-3" style="background: rgba(245, 158, 11, 0.15); color: #fbbf24;">
                                    <i class="fas fa-shield-halved"></i>
                                </div>
                                <div class="bb-assurance-item-text">
                                    <strong class="text-white d-block font-weight-bold" style="font-size: 13.5px; line-height: 1.3;">১০০% অরিজিনাল ও সেরা কোয়ালিটি</strong>
                                    <small class="text-white-50 d-block" style="font-size: 11.5px; line-height: 1.4;">কোনো সমস্যা পেলে ৭ দিনের মধ্যে সহজ রিটার্ন/এক্সচেঞ্জ</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@push('styles')
<style>
    .bb-shop-showcase {
        background: linear-gradient(135deg, #0b1320 0%, #0f172a 60%, #042f24 100%);
        padding: 40px 0 44px;
        position: relative;
        overflow: hidden;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    }
    .bb-shop-showcase:before {
        content: "";
        position: absolute;
        width: 500px;
        height: 500px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(var(--brand-rgb), 0.15) 0%, transparent 70%);
        right: -100px;
        top: -150px;
        pointer-events: none;
    }

    .bb-category-pill {
        display: inline-flex;
        align-items: center;
        padding: 5px 12px;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.14);
        border-radius: 4px;
        color: #f1f5f9;
        font-size: 12px;
        font-weight: 600;
        text-decoration: none !important;
        transition: all 0.2s ease;
    }
    .bb-category-pill:hover {
        background: var(--brand);
        border-color: var(--brand);
        color: #ffffff;
        transform: translateY(-2px);
    }

    .bb-shop-assurance-card {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.12);
        backdrop-filter: blur(16px);
        border-radius: 6px;
        padding: 20px 22px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
    }
    .bb-assurance-icon-sparkle {
        width: 34px;
        height: 34px;
        min-width: 34px;
        border-radius: 4px;
        background: rgba(var(--brand-rgb), 0.2);
        color: var(--brand);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        flex-shrink: 0;
    }
    .bb-assurance-item {
        display: flex;
        align-items: center;
    }
    .bb-assurance-item-icon {
        width: 40px;
        height: 40px;
        min-width: 40px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        flex-shrink: 0;
    }
    .bb-assurance-item-text {
        flex: 1 1 auto;
        min-width: 0;
    }

    /* Products Catalog Block Polish */
    .bb-shop-products-block {
        background: #f8fafc;
        padding: 28px 0 60px;
    }
    .bb-shop-products-block .products-view__options {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 16px 22px;
        margin-bottom: 24px;
        box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.05);
    }
    .bb-shop-products-block .view-options__legend {
        color: #1e293b;
        font-size: 15px;
        font-weight: 700;
    }

    @media (max-width: 991px) {
        .bb-shop-showcase {
            padding: 30px 0 36px;
        }
        .bb-shop-assurance-card {
            margin-top: 10px;
            padding: 18px;
        }
    }
</style>
@endpush
