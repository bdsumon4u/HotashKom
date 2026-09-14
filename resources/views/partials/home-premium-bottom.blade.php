<style>
    .bb-modern-experience {
        background: #f8fafc;
        padding: 56px 0 64px;
        position: relative;
        overflow: hidden;
    }

    /* Ambient background blur accents */
    .bb-modern-experience::before,
    .bb-modern-experience::after {
        content: "";
        position: absolute;
        width: 320px;
        height: 320px;
        border-radius: 50%;
        filter: blur(80px);
        opacity: 0.45;
        pointer-events: none;
        z-index: 0;
    }
    .bb-modern-experience::before {
        top: -40px;
        left: -80px;
        background: radial-gradient(circle, rgba(234, 88, 12, 0.18), transparent 70%);
    }
    .bb-modern-experience::after {
        bottom: -60px;
        right: -80px;
        background: radial-gradient(circle, rgba(37, 99, 235, 0.12), transparent 70%);
    }

    .bb-exp-inner {
        position: relative;
        z-index: 1;
    }

    /* Section Header */
    .bb-exp-header {
        text-align: center;
        max-width: 680px;
        margin: 0 auto 40px;
    }

    .bb-exp-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 14px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 999px;
        color: var(--brand, #ca3d1c);
        font-size: 13px;
        font-weight: 800;
        letter-spacing: 0.02em;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
        margin-bottom: 12px;
    }

    .bb-exp-header h2 {
        font-size: clamp(24px, 3.2vw, 34px);
        font-weight: 800;
        color: #0f172a;
        letter-spacing: -0.02em;
        line-height: 1.3;
        margin-bottom: 10px;
    }

    .bb-exp-header p {
        font-size: 15px;
        color: #64748b;
        line-height: 1.7;
        margin: 0;
    }

    /* 4 Trust Feature Cards (Bento-style grid) */
    .bb-exp-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 28px;
    }

    .bb-exp-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 24px 20px;
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative;
        box-shadow: 0 2px 10px rgba(15, 23, 42, 0.03);
        display: flex;
        flex-direction: column;
    }

    .bb-exp-card:hover {
        transform: translateY(-4px);
        border-color: #cbd5e1;
        box-shadow: 0 12px 24px -6px rgba(15, 23, 42, 0.08);
    }

    .bb-exp-card__icon-wrap {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        margin-bottom: 16px;
        transition: transform 0.25s ease;
    }

    .bb-exp-card:hover .bb-exp-card__icon-wrap {
        transform: scale(1.08);
    }

    .bb-exp-card--1 .bb-exp-card__icon-wrap {
        background: #fff7ed;
        color: #ea580c;
        border: 1px solid #ffedd5;
    }
    .bb-exp-card--2 .bb-exp-card__icon-wrap {
        background: #eff6ff;
        color: #2563eb;
        border: 1px solid #dbeafe;
    }
    .bb-exp-card--3 .bb-exp-card__icon-wrap {
        background: #f0fdf4;
        color: #16a34a;
        border: 1px solid #dcfce7;
    }
    .bb-exp-card--4 .bb-exp-card__icon-wrap {
        background: #faf5ff;
        color: #9333ea;
        border: 1px solid #f3e8ff;
    }

    .bb-exp-card h3 {
        font-size: 16.5px;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 8px;
        line-height: 1.35;
    }

    .bb-exp-card p {
        font-size: 13.5px;
        color: #64748b;
        line-height: 1.65;
        margin: 0;
    }

    /* Modern Journey / 3-Step Process Banner */
    .bb-journey-banner {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        border-radius: 14px;
        padding: 34px 34px 30px;
        color: #ffffff;
        position: relative;
        overflow: hidden;
        box-shadow: 0 16px 32px -8px rgba(15, 23, 42, 0.18);
    }

    .bb-journey-banner::before {
        content: "";
        position: absolute;
        top: 0;
        right: 0;
        width: 320px;
        height: 100%;
        background: radial-gradient(circle at top right, rgba(234, 88, 12, 0.16), transparent 70%);
        pointer-events: none;
    }

    .bb-journey-top {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 24px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        padding-bottom: 18px;
    }

    .bb-journey-title-wrap h3 {
        font-size: clamp(20px, 2.5vw, 25px);
        font-weight: 800;
        color: #ffffff;
        margin: 0 0 6px;
        letter-spacing: -0.01em;
    }

    .bb-journey-title-wrap p {
        font-size: 14px;
        color: #94a3b8;
        margin: 0;
    }

    .bb-journey-tag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.15);
        color: #f8fafc;
        font-size: 12.5px;
        font-weight: 700;
        padding: 5px 14px;
        border-radius: 999px;
        white-space: nowrap;
    }

    .bb-journey-steps {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        position: relative;
    }

    .bb-journey-step {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 10px;
        padding: 20px;
        position: relative;
        transition: all 0.2s ease;
    }

    .bb-journey-step:hover {
        background: rgba(255, 255, 255, 0.08);
        border-color: rgba(255, 255, 255, 0.18);
        transform: translateY(-2px);
    }

    .bb-step-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 12px;
    }

    .bb-step-num {
        font-size: 13px;
        font-weight: 800;
        color: var(--brand, #ea580c);
        background: rgba(234, 88, 12, 0.15);
        padding: 3px 10px;
        border-radius: 6px;
    }

    .bb-step-icon {
        font-size: 18px;
        color: #94a3b8;
    }

    .bb-journey-step h4 {
        font-size: 16px;
        font-weight: 750;
        color: #f8fafc;
        margin: 0 0 6px;
    }

    .bb-journey-step p {
        font-size: 13px;
        color: #94a3b8;
        line-height: 1.6;
        margin: 0;
    }

    /* Bottom Quick Stats Bar */
    .bb-quick-stats {
        margin-top: 20px;
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 12px;
    }

    .bb-stat-item {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 12px 16px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .bb-stat-icon {
        color: var(--brand, #ca3d1c);
        font-size: 18px;
        min-width: 22px;
        text-align: center;
    }

    .bb-stat-text strong {
        display: block;
        font-size: 13px;
        color: #0f172a;
        font-weight: 750;
        line-height: 1.2;
    }

    .bb-stat-text span {
        font-size: 11.5px;
        color: #64748b;
    }

    /* Responsive */
    @media (max-width: 991.98px) {
        .bb-exp-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        .bb-journey-steps {
            grid-template-columns: 1fr;
            gap: 12px;
        }
        .bb-journey-top {
            flex-direction: column;
            align-items: flex-start;
        }
        .bb-quick-stats {
            grid-template-columns: repeat(2, 1fr);
        }
        .bb-journey-banner {
            padding: 24px 20px;
        }
    }

    @media (max-width: 575.98px) {
        .bb-modern-experience {
            padding: 40px 0 48px;
        }
        .bb-exp-grid {
            grid-template-columns: 1fr;
            gap: 12px;
        }
        .bb-exp-card {
            padding: 18px 16px;
        }
        .bb-quick-stats {
            grid-template-columns: 1fr;
        }
    }
</style>

<section class="bb-modern-experience">
    <div class="container bb-exp-inner">
        {{-- Section Heading --}}
        <div class="bb-exp-header">
            <div class="bb-exp-badge">
                <i class="fas fa-shield-alt"></i> {{ ($company->name ?? config('app.name')) }}-এর বিশেষ সেবা
            </div>
            <h2>সহজ ও নিশ্চিন্ত অনলাইন শপিং</h2>
            <p>সেরা মানের পণ্য, দ্রুত ডেলিভারি ও ১০০% নির্ভরযোগ্য কাস্টমার সাপোর্ট নিয়ে আমরা আছি সবসময় আপনার পাশে।</p>
        </div>

        {{-- 4 Pillars Grid --}}
        <div class="bb-exp-grid">
            <article class="bb-exp-card bb-exp-card--1">
                <div class="bb-exp-card__icon-wrap">
                    <i class="fas fa-hand-holding-dollar"></i>
                </div>
                <h3>ক্যাশ অন ডেলিভারি</h3>
                <p>পণ্য হাতে পেয়ে যাচাই করে মূল্য পরিশোধ করার সম্পূর্ণ নিশ্চিন্ত ও নিরাপদ সুবিধা।</p>
            </article>

            <article class="bb-exp-card bb-exp-card--2">
                <div class="bb-exp-card__icon-wrap">
                    <i class="fas fa-award"></i>
                </div>
                <h3>অরিজিনাল পণ্য নিশ্চয়তা</h3>
                <p>সঠিক বিবরণ ও প্রিমিয়াম কোয়ালিটি সম্পন্ন পণ্য সরাসরি আপনার ঠিকানায়।</p>
            </article>

            <article class="bb-exp-card bb-exp-card--3">
                <div class="bb-exp-card__icon-wrap">
                    <i class="fas fa-truck-fast"></i>
                </div>
                <h3>সারা দেশে দ্রুত ডেলিভারি</h3>
                <p>ঢাকা এবং দেশের যেকোনো প্রান্তে দ্রুততম সময়ে যত্নসহকারে হোম ডেলিভারি।</p>
            </article>

            <article class="bb-exp-card bb-exp-card--4">
                <div class="bb-exp-card__icon-wrap">
                    <i class="fas fa-headset"></i>
                </div>
                <h3>আন্তরিক গ্রাহক সেবা</h3>
                <p>অর্ডার সংক্রান্ত যেকোনো প্রশ্ন বা সহযোগিতায় আমাদের হেল্পলাইন সবসময় প্রস্তুত।</p>
            </article>
        </div>

        {{-- 3-Step Journey Banner --}}
        <div class="bb-journey-banner">
            <div class="bb-journey-top">
                <div class="bb-journey-title-wrap">
                    <h3>অর্ডার করার সহজ ৩টি ধাপ</h3>
                    <p>কয়েকটি ক্লিকেই সম্পন্ন করুন আপনার অর্ডার</p>
                </div>
                <div class="bb-journey-tag">
                    <i class="fas fa-bolt text-warning"></i> ইনস্ট্যান্ট অর্ডার
                </div>
            </div>

            <div class="bb-journey-steps">
                <div class="bb-journey-step">
                    <div class="bb-step-top">
                        <span class="bb-step-num">ধাপ ০১</span>
                        <i class="fas fa-cart-shopping bb-step-icon"></i>
                    </div>
                    <h4>পণ্য পছন্দ করুন</h4>
                    <p>পছন্দের পণ্য সিলেক্ট করে সম্পূর্ণ স্পেসিফিকেশন ও মূল্য দেখে নিন।</p>
                </div>

                <div class="bb-journey-step">
                    <div class="bb-step-top">
                        <span class="bb-step-num">ধাপ ০২</span>
                        <i class="fas fa-file-invoice bb-step-icon"></i>
                    </div>
                    <h4>অর্ডার কনফার্ম করুন</h4>
                    <p>নাম, মোবাইল নম্বর ও ডেলিভারি ঠিকানা দিয়ে সরাসরি অর্ডার নিশ্চিত করুন।</p>
                </div>

                <div class="bb-journey-step">
                    <div class="bb-step-top">
                        <span class="bb-step-num">ধাপ ০৩</span>
                        <i class="fas fa-box-open bb-step-icon"></i>
                    </div>
                    <h4>পণ্য বুঝে নিন</h4>
                    <p>ডেলিভারি ম্যানের কাছ থেকে পার্সেল গ্রহণ করে মূল্য পরিশোধ করুন।</p>
                </div>
            </div>
        </div>

        {{-- Quick Stats Bar --}}
        <div class="bb-quick-stats">
            <div class="bb-stat-item">
                <i class="fas fa-map-location-dot bb-stat-icon"></i>
                <div class="bb-stat-text">
                    <strong>৬৪ জেলায় ডেলিভারি</strong>
                    <span>সারা দেশে কুরিয়ার সুবিধা</span>
                </div>
            </div>
            <div class="bb-stat-item">
                <i class="fas fa-box-archive bb-stat-icon"></i>
                <div class="bb-stat-text">
                    <strong>নিরাপদ প্যাকেজিং</strong>
                    <span>সুরক্ষিত ডেলিভারি প্যাকেজ</span>
                </div>
            </div>
            <div class="bb-stat-item">
                <i class="fas fa-phone-volume bb-stat-icon"></i>
                <div class="bb-stat-text">
                    <strong>সহজ যোগাযোগ</strong>
                    <span>সরাসরি ফোন ও হোয়াটসঅ্যাপ</span>
                </div>
            </div>
            <div class="bb-stat-item">
                <i class="fas fa-badge-check bb-stat-icon"></i>
                <div class="bb-stat-text">
                    <strong>১০০% নিরাপদ শপিং</strong>
                    <span>বিশ্বাস ও নির্ভরতার প্রতীক</span>
                </div>
            </div>
        </div>
    </div>
</section>
