<style>
    .nm-home-premium {
        --nm-ink: #173226;
        --nm-text: #63746b;
        padding: 68px 0;
        background: var(--brand-soft);
    }

    .nm-home-premium__heading {
        max-width: 720px;
        margin: 0 auto 36px;
        text-align: center;
    }

    .nm-home-premium__eyebrow {
        display: inline-block;
        margin-bottom: 10px;
        color: var(--nm-green-dark);
        font-size: 14px;
        font-weight: 800;
    }

    .nm-home-premium__heading h2 {
        margin: 0 0 12px;
        color: var(--nm-ink);
        font-size: clamp(27px, 3vw, 38px);
        font-weight: 800;
        line-height: 1.3;
    }

    .nm-home-premium__heading p {
        margin: 0;
        color: var(--nm-text);
        font-size: 16px;
        line-height: 1.8;
    }

    .nm-home-benefits {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 57px;
    }

    .nm-home-benefit {
        padding: 25px 20px;
        background: #fff;
        border: 1px solid #dcefe4;
        border-radius: 14px;
        box-shadow: 0 8px 22px rgba(20, 69, 43, .06);
    }

    .nm-home-benefit__icon {
        display: grid;
        width: 43px;
        height: 43px;
        margin-bottom: 16px;
        place-items: center;
        border-radius: 10px;
        background: var(--brand-soft);
        color: var(--nm-green-dark);
        font-size: 21px;
        font-weight: 800;
    }

    .nm-home-benefit h3 {
        margin: 0 0 8px;
        color: var(--nm-ink);
        font-size: 17px;
        font-weight: 750;
    }

    .nm-home-benefit p {
        margin: 0;
        color: var(--nm-text);
        font-size: 14px;
        line-height: 1.7;
    }

    .nm-home-journey {
        overflow: hidden;
        padding: 47px;
        background: linear-gradient(135deg, var(--nm-green-dark), var(--nm-green));
        border-radius: 17px;
        color: #fff;
        position: relative;
    }

    .nm-home-journey::after {
        content: "";
        position: absolute;
        top: -165px;
        right: -80px;
        width: 340px;
        height: 340px;
        border: 42px solid rgba(255, 255, 255, .12);
        border-radius: 50%;
    }

    .nm-home-journey__inner {
        position: relative;
        z-index: 1;
    }

    .nm-home-journey__top {
        max-width: 660px;
        margin-bottom: 31px;
    }

    .nm-home-journey__top span {
        display: inline-block;
        margin-bottom: 9px;
        color: #d8ffe8;
        font-size: 14px;
        font-weight: 700;
    }

    .nm-home-journey h2 {
        margin: 0 0 11px;
        color: #fff;
        font-size: clamp(26px, 3vw, 35px);
        font-weight: 800;
        line-height: 1.3;
    }

    .nm-home-journey p {
        margin: 0;
        color: #effff5;
        font-size: 15px;
        line-height: 1.8;
    }

    .nm-home-steps {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 14px;
    }

    .nm-home-step {
        padding: 18px;
        background: rgba(255, 255, 255, .12);
        border: 1px solid rgba(255, 255, 255, .18);
        border-radius: 11px;
    }

    .nm-home-step b {
        display: block;
        margin-bottom: 11px;
        color: #c6ffdc;
        font-size: 14px;
    }

    .nm-home-step h3 {
        margin: 0 0 6px;
        color: #fff;
        font-size: 17px;
        font-weight: 750;
    }

    .nm-home-step p {
        color: #e3f9eb;
        font-size: 13px;
        line-height: 1.65;
    }

    .nm-home-categories {
        padding: 61px 0 3px;
    }

    .nm-home-categories__top {
        display: flex;
        align-items: end;
        justify-content: space-between;
        gap: 18px;
        margin-bottom: 22px;
    }

    .nm-home-categories h2 {
        margin: 0;
        color: var(--nm-ink);
        font-size: 28px;
        font-weight: 800;
    }

    .nm-home-categories__all,
    .nm-home-cta__button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 45px;
        padding: 9px 18px;
        border-radius: 8px;
        background: var(--nm-green);
        color: #fff !important;
        font-size: 14px;
        font-weight: 750;
        text-decoration: none !important;
        transition: .2s ease;
    }

    .nm-home-categories__all:hover,
    .nm-home-cta__button:hover {
        background: var(--nm-green-dark);
        transform: translateY(-2px);
    }

    .nm-home-category-list {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .nm-home-category-list a {
        padding: 10px 14px;
        border: 1px solid #d3ebdd;
        border-radius: 8px;
        background: #fff;
        color: #315442 !important;
        font-size: 14px;
        font-weight: 700;
        text-decoration: none !important;
        transition: .2s ease;
    }

    .nm-home-category-list a:hover {
        border-color: var(--nm-green);
        background: #e9fbf0;
        color: var(--nm-green-dark) !important;
    }

    .nm-home-cta {
        margin-top: 56px;
        padding: 52px 25px;
        border-radius: 17px;
        background: #183d2b;
        text-align: center;
    }

    .nm-home-cta span {
        color: #9af1bd;
        font-size: 14px;
        font-weight: 750;
    }

    .nm-home-cta h2 {
        margin: 10px 0;
        color: #fff;
        font-size: clamp(27px, 3vw, 38px);
        font-weight: 800;
    }

    .nm-home-cta p {
        max-width: 620px;
        margin: 0 auto 24px;
        color: #d5e8dc;
        font-size: 15px;
        line-height: 1.8;
    }

    @media (max-width: 991px) {
        .nm-home-benefits {
            grid-template-columns: repeat(2, 1fr);
        }

        .nm-home-steps {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 575px) {
        .nm-home-premium {
            padding: 48px 0;
        }

        .nm-home-benefits {
            grid-template-columns: 1fr;
            margin-bottom: 42px;
        }

        .nm-home-journey {
            padding: 33px 22px;
        }

        .nm-home-categories__top {
            display: block;
        }

        .nm-home-categories__all {
            margin-top: 16px;
        }
    }
</style>

<section class="nm-home-premium">
    <div class="container">
        <div class="nm-home-premium__heading">
            <span class="nm-home-premium__eyebrow">{{ ($company->name ?? config('app.name')) }}-এর প্রতিশ্রুতি</span>
            <h2>সহজ ও নিশ্চিন্ত অনলাইন শপিং</h2>
            <p>দৈনন্দিন প্রয়োজনীয় পণ্য সহজে খুঁজুন, সঠিক তথ্য দেখে অর্ডার করুন এবং আপনার ঠিকানায় নিরাপদে গ্রহণ করুন।</p>
        </div>

        <div class="nm-home-benefits">
            <article class="nm-home-benefit">
                <div class="nm-home-benefit__icon">৳</div>
                <h3>ক্যাশ অন ডেলিভারি</h3>
                <p>অধিকাংশ পণ্যের মূল্য পণ্য হাতে পাওয়ার পর পরিশোধের সুবিধা।</p>
            </article>

            <article class="nm-home-benefit">
                <div class="nm-home-benefit__icon">□</div>
                <h3>নিরাপদ প্যাকেজিং</h3>
                <p>পরিবহনের সময় ক্ষতির ঝুঁকি কমাতে যত্নসহকারে পণ্য প্যাকেটজাত করা হয়।</p>
            </article>

            <article class="nm-home-benefit">
                <div class="nm-home-benefit__icon">◉</div>
                <h3>সারা দেশে ডেলিভারি</h3>
                <p>বাংলাদেশের বিভিন্ন এলাকায় আপনার প্রয়োজনীয় পণ্য পৌঁছে দেওয়ার চেষ্টা করি।</p>
            </article>

            <article class="nm-home-benefit">
                <div class="nm-home-benefit__icon">?</div>
                <h3>দায়িত্বশীল সহায়তা</h3>
                <p>অর্ডার, ডেলিভারি বা পণ্যসংক্রান্ত তথ্যের জন্য আমাদের সঙ্গে যোগাযোগ করুন।</p>
            </article>
        </div>

        <div class="nm-home-journey">
            <div class="nm-home-journey__inner">
                <div class="nm-home-journey__top">
                    <span>কয়েকটি সহজ ধাপ</span>
                    <h2>আপনার পছন্দের পণ্য অর্ডার করুন সহজেই</h2>
                    <p>পণ্যের বিবরণ দেখে পছন্দ করুন, তথ্য দিয়ে অর্ডার সম্পন্ন করুন এবং delivery update-এর জন্য অপেক্ষা করুন।</p>
                </div>

                <div class="nm-home-steps">
                    <div class="nm-home-step">
                        <b>০১</b>
                        <h3>পণ্য পছন্দ করুন</h3>
                        <p>আপনার প্রয়োজনের পণ্যটি খুঁজে সম্পূর্ণ তথ্য ও মূল্য দেখে নিন।</p>
                    </div>

                    <div class="nm-home-step">
                        <b>০২</b>
                        <h3>অর্ডার নিশ্চিত করুন</h3>
                        <p>সঠিক নাম, ফোন নম্বর ও delivery address দিয়ে অর্ডার করুন।</p>
                    </div>

                    <div class="nm-home-step">
                        <b>০৩</b>
                        <h3>পণ্য গ্রহণ করুন</h3>
                        <p>কুরিয়ারে আপনার পণ্য পৌঁছালে যাচাই করে গ্রহণ করুন।</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="nm-home-categories">
            <div class="nm-home-categories__top">
                <h2>জনপ্রিয় বিভাগগুলো দেখুন</h2>
                <a href="{{ route('categories') }}" class="nm-home-categories__all">সব বিভাগ দেখুন</a>
            </div>

            <div class="nm-home-category-list">
                @foreach(categories()->take(8) as $category)
                    <a href="{{ route('category.show', $category) }}">{{ $category->name }}</a>
                @endforeach
            </div>
        </div>

        <div class="nm-home-cta">
            <span>আজই শুরু করুন</span>
            <h2>আপনার প্রয়োজনীয় পণ্য খুঁজে নিন</h2>
            <p>নিত্যপ্রয়োজনীয়, কিচেন, গ্যাজেট, কিডস ও লাইফস্টাইল পণ্য এক জায়গায় সহজে দেখুন ও অর্ডার করুন।</p>
            <a href="{{ route('products.index') }}" class="nm-home-cta__button">সব পণ্য দেখুন</a>
        </div>
    </div>
</section>
