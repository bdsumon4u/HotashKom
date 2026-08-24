@php
    $siteBrand = data_get(setting('company'), 'name') ?: config('app.name');
    $sitePhone = data_get(setting('company'), 'phone') ?: '';
    $siteEmail = data_get(setting('company'), 'email') ?: ('support@' . request()->getHost());
    $siteAddress = data_get(setting('company'), 'address') ?: '';
@endphp
<section class="neh-about">
    <section class="neh-about-hero">
        <div class="container">
            <div class="neh-about-hero__content">
                <span class="neh-about-badge">বিশ্বাসের সঙ্গে অনলাইন শপিং</span>
                <h1>প্রয়োজনের পণ্য,<br><em>নিশ্চিন্ত কেনাকাটা</em></h1>
                <p>{{ $siteBrand }} আপনার দৈনন্দিন প্রয়োজনীয় পণ্য সহজে খুঁজে পাওয়া, অর্ডার করা এবং নিরাপদে গ্রহণ করার নির্ভরযোগ্য অনলাইন ঠিকানা।</p>
                <div class="neh-about-actions">
                    <a href="{{ url('/') }}" class="neh-btn neh-btn--white">পণ্য দেখুন</a>
                    <a href="{{ url('/contact-us') }}" class="neh-btn neh-btn--outline">যোগাযোগ করুন</a>
                </div>
            </div>
        </div>
    </section>

    <div class="container">
        <section class="neh-trust-grid">
            <article class="neh-trust-card">
                <div class="neh-icon">✓</div>
                <h2>বিশ্বাসযোগ্য পণ্য</h2>
                <p>পণ্যের গুরুত্বপূর্ণ তথ্য ও মূল্য পরিষ্কারভাবে দেখানোর চেষ্টা করি।</p>
            </article>
            <article class="neh-trust-card">
                <div class="neh-icon">৳</div>
                <h2>ক্যাশ অন ডেলিভারি</h2>
                <p>অধিকাংশ পণ্যের মূল্য পণ্য হাতে পাওয়ার পর পরিশোধের সুবিধা।</p>
            </article>
            <article class="neh-trust-card">
                <div class="neh-icon">□</div>
                <h2>নিরাপদ প্যাকেজিং</h2>
                <p>পরিবহনের সময় ক্ষতির ঝুঁকি কমাতে পণ্য যত্নসহকারে প্যাকেটজাত করা হয়।</p>
            </article>
            <article class="neh-trust-card">
                <div class="neh-icon">◉</div>
                <h2>দায়িত্বশীল সহায়তা</h2>
                <p>অর্ডার, ডেলিভারি ও পণ্যসংক্রান্ত সহায়তায় আমরা আপনার পাশে আছি।</p>
            </article>
        </section>

        <section class="neh-story">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <span class="neh-label">আমাদের গল্প</span>
                    <h2>সহজ, স্বচ্ছ ও নির্ভরযোগ্য শপিং অভিজ্ঞতার জন্য {{ $siteBrand }}</h2>
                </div>
                <div class="col-lg-6">
                    <p>{{ $siteAddress ? ($siteAddress . ' থেকে পরিচালিত ') : '' }}{{ $siteBrand }} বাংলাদেশের বিভিন্ন এলাকার গ্রাহকদের কাছে প্রয়োজনীয় ও ব্যবহারিক পণ্য পৌঁছে দেওয়ার লক্ষ্য নিয়ে কাজ করছে।</p>
                    <p>আমরা বিশ্বাস করি, ভালো অনলাইন শপিং মানে শুধু পণ্য কেনা নয়। সঠিক তথ্য, স্বচ্ছ মূল্য, সহজ অর্ডার, সময়মতো ডেলিভারি এবং প্রয়োজনের সময় সহযোগিতা মিলেই তৈরি হয় একটি ভালো অভিজ্ঞতা।</p>
                </div>
            </div>
        </section>

        <section class="neh-process">
            <div class="neh-section-title">
                <span class="neh-label">কীভাবে কাজ করি</span>
                <h2>আপনার অর্ডার, আমাদের দায়িত্ব</h2>
                <p>প্রতিটি ধাপে পরিষ্কার ও সহজ একটি কেনাকাটার অভিজ্ঞতা দেওয়ার চেষ্টা করি।</p>
            </div>
            <div class="row">
                <div class="col-sm-6 col-lg-3"><div class="neh-step"><b>০১</b><h3>পণ্য নির্বাচন</h3><p>বিবরণ, মূল্য ও প্রয়োজনীয় তথ্য দেখে পছন্দের পণ্য নির্বাচন করুন।</p></div></div>
                <div class="col-sm-6 col-lg-3"><div class="neh-step"><b>০২</b><h3>অর্ডার নিশ্চিতকরণ</h3><p>সঠিক তথ্য যাচাই করে অর্ডার প্রক্রিয়াটি নিশ্চিত করা হয়।</p></div></div>
                <div class="col-sm-6 col-lg-3"><div class="neh-step"><b>০৩</b><h3>নিরাপদ প্যাকেজিং</h3><p>পণ্যটি যত্নসহকারে প্যাকেটজাত করে কুরিয়ারে পাঠানো হয়।</p></div></div>
                <div class="col-sm-6 col-lg-3"><div class="neh-step"><b>০৪</b><h3>পণ্য গ্রহণ</h3><p>সারা বাংলাদেশে আপনার ঠিকানায় পণ্য পৌঁছে দেওয়ার চেষ্টা করি।</p></div></div>
            </div>
        </section>

        <section class="neh-delivery">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <span class="neh-label">সারা বাংলাদেশে ডেলিভারি</span>
                    <h2>আপনার প্রয়োজনীয় পণ্য পৌঁছে যাবে আপনার দোরগোড়ায়</h2>
                    <p>ঢাকা ও আশেপাশের এলাকায় সাধারণত ১–২ কার্যদিবস, অন্যান্য এলাকায় ৩–৫ কার্যদিবস এবং দুর্গম এলাকায় ৩–৭ কার্যদিবস সময় লাগতে পারে।</p>
                </div>
                <div class="col-lg-6">
                    <div class="neh-delivery-card">
                        <div><strong>১–২ দিন</strong><span>ঢাকা ও পার্শ্ববর্তী এলাকা</span></div>
                        <div><strong>৩–৫ দিন</strong><span>বাংলাদেশের অন্যান্য এলাকা</span></div>
                        <div><strong>৩–৭ দিন</strong><span>দুর্গম বা বিশেষ এলাকা</span></div>
                    </div>
                </div>
            </div>
        </section>

        <section class="neh-contact">
            <span>প্রয়োজন হলে কথা বলুন</span>
            <h2>আমরা আপনার পাশে আছি</h2>
            <p>পণ্যের তথ্য, অর্ডার, ডেলিভারি, রিটার্ন বা রিফান্ড নিয়ে সহায়তা প্রয়োজন হলে আমাদের সঙ্গে যোগাযোগ করুন।</p>
            <div class="neh-contact__info">
                @if($sitePhone)
                <a href="tel:{{ $sitePhone }}">{{ $sitePhone }}</a>
                @endif
                <!--email_off--><a href="mailto:{{ $siteEmail }}">{{ $siteEmail }}</a><!--/email_off-->
            </div>
            <a href="{{ url('/') }}" class="neh-btn neh-btn--green">আজই শপিং করুন</a>
        </section>
    </div>
</section>

<style>
.neh-about{color:#23352c;padding-bottom:55px}.neh-about-hero{background:linear-gradient(135deg,#008C4B,#00C46A);color:#fff;padding:88px 0 96px;position:relative;overflow:hidden}.neh-about-hero:after{content:"";position:absolute;right:-100px;top:-190px;width:430px;height:430px;border:52px solid #ffffff18;border-radius:50%}.neh-about-hero__content{max-width:700px;position:relative;z-index:1}.neh-about-badge,.neh-label{display:inline-block;font-size:14px;font-weight:700}.neh-about-badge{padding:8px 15px;border:1px solid #ffffff42;border-radius:50px;background:#ffffff16;margin-bottom:17px}.neh-label{color:#008C4B;margin-bottom:12px}.neh-about h1{font-size:clamp(35px,5vw,58px);line-height:1.18;font-weight:800;letter-spacing:-.7px;margin:0 0 18px;color:#fff}.neh-about h1 em{font-style:normal;color:#d9ffe9}.neh-about-hero p{font-size:18px;line-height:1.8;margin:0;max-width:650px}.neh-about-actions{display:flex;flex-wrap:wrap;gap:12px;margin-top:29px}.neh-btn{display:inline-flex;align-items:center;justify-content:center;min-height:48px;border-radius:8px;padding:10px 23px;font-weight:700;text-decoration:none!important;transition:.2s}.neh-btn:hover{transform:translateY(-2px)}.neh-btn--white{background:#fff;color:#008C4B!important}.neh-btn--outline{border:1px solid #ffffff80;color:#fff!important}.neh-btn--green{background:#00C46A;color:#fff!important}.neh-trust-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:18px;margin-top:-42px;position:relative;z-index:2}.neh-trust-card{background:#fff;border:1px solid #e6eee9;box-shadow:0 12px 30px #183b2b14;padding:27px 22px;border-radius:12px}.neh-icon{display:flex;width:43px;height:43px;align-items:center;justify-content:center;border-radius:10px;background:#e9fbf1;color:#008C4B;font-size:22px;font-weight:800;margin-bottom:16px}.neh-trust-card h2,.neh-step h3{font-size:18px;font-weight:750;color:#1b3026;margin:0 0 9px}.neh-trust-card p,.neh-step p{color:#64736b;font-size:14px;line-height:1.7;margin:0}.neh-story{padding:92px 6% 75px}.neh-story h2,.neh-delivery h2,.neh-section-title h2,.neh-contact h2{font-size:clamp(27px,3vw,38px);font-weight:800;line-height:1.35;color:#1b3026;margin:0 0 16px}.neh-story p,.neh-delivery p{color:#607067;font-size:16px;line-height:1.85}.neh-process{padding:61px 42px;background:#f3faf6;border-radius:16px}.neh-section-title{text-align:center;max-width:700px;margin:0 auto 36px}.neh-section-title p{color:#68776f;margin:0}.neh-step{border-left:2px solid #9CE6BF;min-height:155px;padding:4px 14px 4px 18px}.neh-step b{display:block;color:#008C4B;font-size:14px;margin-bottom:14px}.neh-delivery{padding:83px 6%}.neh-delivery-card{display:grid;gap:12px;padding:22px;background:#008C4B;border-radius:14px}.neh-delivery-card div{padding:17px 18px;background:#ffffff18;border-radius:9px;color:#fff}.neh-delivery-card strong{display:block;font-size:24px}.neh-delivery-card span{font-size:14px;opacity:.88}.neh-contact{background:#143b2b;color:#fff;text-align:center;padding:62px 25px;border-radius:16px}.neh-contact>span{color:#B8F5D0;font-size:14px;font-weight:700}.neh-contact h2{color:#fff;margin-top:10px}.neh-contact p{max-width:680px;margin:0 auto;color:#d0e2d8;line-height:1.8}.neh-contact__info{display:flex;justify-content:center;flex-wrap:wrap;gap:10px;margin:25px 0}.neh-contact__info a{background:#ffffff12;border:1px solid #ffffff20;border-radius:7px;padding:9px 14px;color:#fff!important;text-decoration:none}@media(max-width:991px){.neh-trust-grid{grid-template-columns:repeat(2,1fr)}.neh-delivery-card{margin-top:24px}}@media(max-width:575px){.neh-about-hero{padding:64px 0 76px}.neh-about-hero p{font-size:16px}.neh-trust-grid{grid-template-columns:1fr;margin-top:-30px}.neh-story,.neh-delivery{padding:58px 4%}.neh-process{padding:45px 24px}.neh-step{min-height:auto;margin-bottom:25px}}
</style>
