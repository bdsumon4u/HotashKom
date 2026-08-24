

@php
    $siteBrand = data_get(setting('company'), 'name') ?: config('app.name');
    $sitePhone = data_get(setting('company'), 'phone') ?: '';
    $siteEmail = data_get(setting('company'), 'email') ?: ('support@' . request()->getHost());
    $siteAddress = data_get(setting('company'), 'address') ?: '';
    $siteMapEcode = data_get(setting('company'), 'gmap_ecode') ?: null;
@endphp

<div class="nm-contact-page">

    <style>
        .nm-contact-page {
            --nm-ink: #101828;
            --nm-text: #475467;
            --nm-muted: #667085;
            --nm-line: #EAECF0;
            --nm-bg: #F7F9F8;
            --nm-white: #FFFFFF;
            --nm-warn: #FFF8E7;
            --nm-warn-line: #F2D38A;
            --nm-shadow: 0 16px 45px rgba(16, 24, 40, .08);
            --nm-shadow-soft: 0 6px 22px rgba(16, 24, 40, .06);

            color: var(--nm-ink);
            font-family:
                Inter,
                "Noto Sans Bengali",
                system-ui,
                -apple-system,
                BlinkMacSystemFont,
                "Segoe UI",
                sans-serif;

            background:
                linear-gradient(
                    180deg,
                    #F9FCFA 0,
                    #FFFFFF 420px
                );

            padding-bottom: 70px;
        }

        .nm-contact-page *,
        .nm-contact-page *::before,
        .nm-contact-page *::after {
            box-sizing: border-box;
        }

        .nm-contact-shell {
            width: min(1180px, calc(100% - 32px));
            margin: 0 auto;
        }

        .nm-contact-hero {
            position: relative;
            overflow: hidden;
            padding: 70px 0 62px;
            background:
                radial-gradient(
                    circle at 85% 10%,
                    rgba(0,196,106,.19),
                    transparent 27%
                ),
                radial-gradient(
                    circle at 10% 90%,
                    rgba(0,196,106,.08),
                    transparent 25%
                ),
                linear-gradient(
                    135deg,
                    #008C4B 0%,
                    #00A858 56%,
                    #00C46A 100%
                );
            color: #fff;
        }

        .nm-contact-hero::after {
            content: "";
            position: absolute;
            width: 340px;
            height: 340px;
            border: 1px solid rgba(255,255,255,.06);
            border-radius: 50%;
            right: -100px;
            bottom: -190px;
        }

        .nm-contact-hero-inner {
            position: relative;
            z-index: 2;
            max-width: 760px;
        }

        .nm-contact-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 13px;
            margin-bottom: 20px;
            border: 1px solid rgba(255,255,255,.14);
            border-radius: 999px;
            background: rgba(255,255,255,.07);
            color: #D7F8E8;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: .02em;
        }

        .nm-contact-eyebrow-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--nm-green);
            box-shadow: 0 0 0 5px rgba(0,196,106,.13);
        }

        .nm-contact-hero h1 {
            margin: 0;
            color: #fff;
            font-size: clamp(36px, 5vw, 58px);
            line-height: 1.08;
            font-weight: 800;
            letter-spacing: -.035em;
        }

        .nm-contact-hero p {
            max-width: 690px;
            margin: 20px 0 0;
            color: #CFDAD4;
            font-size: 17px;
            line-height: 1.8;
        }

        .nm-contact-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 30px;
        }

        .nm-contact-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            min-height: 48px;
            padding: 0 20px;
            border-radius: 11px;
            text-decoration: none !important;
            font-size: 14px;
            font-weight: 750;
            transition:
                transform .2s ease,
                box-shadow .2s ease,
                background .2s ease;
        }

        .nm-contact-btn:hover {
            transform: translateY(-2px);
        }

        .nm-contact-btn-primary {
            background: var(--nm-green);
            color: #082318 !important;
            box-shadow: 0 10px 24px rgba(0,196,106,.23);
        }

        .nm-contact-btn-primary:hover {
            background: #00B65F;
        }

        .nm-contact-btn-secondary {
            color: #fff !important;
            border: 1px solid rgba(255,255,255,.18);
            background: rgba(255,255,255,.08);
        }

        .nm-contact-btn svg,
        .nm-info-icon svg,
        .nm-help-icon svg,
        .nm-security-icon svg {
            width: 20px;
            height: 20px;
            flex: 0 0 auto;
        }

        .nm-contact-info-wrap {
            position: relative;
            z-index: 4;
            margin-top: -28px;
        }

        .nm-contact-info-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
        }

        .nm-info-card {
            min-width: 0;
            padding: 22px;
            border: 1px solid var(--nm-line);
            border-radius: 18px;
            background: var(--nm-white);
            box-shadow: var(--nm-shadow-soft);
        }

        .nm-info-icon {
            width: 43px;
            height: 43px;
            display: grid;
            place-items: center;
            margin-bottom: 17px;
            border-radius: 12px;
            color: var(--nm-green-dark);
            background: var(--nm-green-soft);
        }

        .nm-info-label {
            margin-bottom: 7px;
            color: var(--nm-muted);
            font-size: 12px;
            font-weight: 750;
            text-transform: uppercase;
            letter-spacing: .055em;
        }

        .nm-info-value {
            color: var(--nm-ink);
            font-size: 15px;
            line-height: 1.65;
            font-weight: 700;
            overflow-wrap: anywhere;
        }

        .nm-info-value a {
            color: inherit;
            text-decoration: none;
        }

        .nm-contact-main {
            padding-top: 58px;
        }

        .nm-section-heading {
            max-width: 680px;
            margin-bottom: 26px;
        }

        .nm-section-kicker {
            display: block;
            margin-bottom: 8px;
            color: var(--nm-green-dark);
            font-size: 13px;
            line-height: 1;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .055em;
        }

        .nm-section-heading h2 {
            margin: 0;
            color: var(--nm-ink);
            font-size: clamp(27px, 3.2vw, 38px);
            line-height: 1.2;
            letter-spacing: -.025em;
        }

        .nm-section-heading p {
            margin: 12px 0 0;
            color: var(--nm-text);
            font-size: 15px;
            line-height: 1.8;
        }

        .nm-contact-form-map-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.05fr) minmax(0, .95fr);
            gap: 24px;
            align-items: stretch;
        }

        .nm-form-card,
        .nm-map-card {
            overflow: hidden;
            border: 1px solid var(--nm-line);
            border-radius: 22px;
            background: #fff;
            box-shadow: var(--nm-shadow);
        }

        .nm-form-card {
            padding: 30px;
        }

        .nm-card-heading {
            margin-bottom: 23px;
        }

        .nm-card-heading h3 {
            margin: 0;
            color: var(--nm-ink);
            font-size: 23px;
            line-height: 1.3;
            letter-spacing: -.018em;
        }

        .nm-card-heading p {
            margin: 8px 0 0;
            color: var(--nm-text);
            font-size: 14px;
            line-height: 1.7;
        }

        .nm-form-card form {
            width: 100%;
        }

        .nm-form-card form > * {
            margin-bottom: 15px;
        }

        .nm-form-card label {
            display: block;
            margin-bottom: 7px;
            color: #344054;
            font-size: 13px;
            font-weight: 700;
        }

        .nm-form-card input:not([type="hidden"]):not([type="checkbox"]):not([type="radio"]),
        .nm-form-card textarea,
        .nm-form-card select {
            width: 100% !important;
            min-height: 49px;
            padding: 12px 14px !important;
            border: 1px solid #D0D5DD !important;
            border-radius: 11px !important;
            outline: none !important;
            background: #fff !important;
            color: var(--nm-ink) !important;
            font-family: inherit !important;
            font-size: 14px !important;
            line-height: 1.5;
            box-shadow: none !important;
            transition:
                border-color .18s ease,
                box-shadow .18s ease;
        }

        .nm-form-card textarea {
            min-height: 145px !important;
            resize: vertical;
        }

        .nm-form-card input:focus,
        .nm-form-card textarea:focus,
        .nm-form-card select:focus {
            border-color: var(--nm-green) !important;
            box-shadow:
                0 0 0 4px rgba(0,196,106,.10)
                !important;
        }

        .nm-form-card button,
        .nm-form-card input[type="submit"] {
            min-height: 49px !important;
            padding: 0 22px !important;
            border: 0 !important;
            border-radius: 11px !important;
            background: var(--nm-green) !important;
            color: #082318 !important;
            font-family: inherit !important;
            font-size: 14px !important;
            font-weight: 800 !important;
            cursor: pointer;
            box-shadow: 0 10px 22px rgba(0,196,106,.18);
            transition:
                transform .18s ease,
                background .18s ease;
        }

        .nm-form-card button:hover,
        .nm-form-card input[type="submit"]:hover {
            background: #00B65F !important;
            transform: translateY(-1px);
        }

        .nm-map-card {
            min-height: 520px;
            display: flex;
            flex-direction: column;
        }

        .nm-map-top {
            padding: 24px 25px 19px;
        }

        .nm-map-top h3 {
            margin: 0;
            color: var(--nm-ink);
            font-size: 21px;
        }

        .nm-map-top p {
            margin: 8px 0 0;
            color: var(--nm-text);
            font-size: 14px;
            line-height: 1.7;
        }

        .nm-map-frame {
            flex: 1;
            min-height: 375px;
            background: #EEF2F0;
        }

        .nm-map-frame iframe {
            display: block !important;
            width: 100% !important;
            height: 100% !important;
            min-height: 375px !important;
            border: 0 !important;
        }

        .nm-order-note {
            display: flex;
            gap: 12px;
            align-items: flex-start;
            margin-top: 20px;
            padding: 15px 17px;
            border-radius: 13px;
            background: #F8FAF9;
            border: 1px solid #E7ECE9;
            color: #475467;
            font-size: 13px;
            line-height: 1.7;
        }

        .nm-order-note strong {
            color: var(--nm-ink);
        }

        .nm-help-section {
            padding-top: 70px;
        }

        .nm-help-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
        }

        .nm-help-card {
            padding: 23px;
            border: 1px solid var(--nm-line);
            border-radius: 18px;
            background: #fff;
            transition:
                transform .2s ease,
                box-shadow .2s ease,
                border-color .2s ease;
        }

        .nm-help-card:hover {
            transform: translateY(-3px);
            border-color: rgba(0,196,106,.28);
            box-shadow: var(--nm-shadow-soft);
        }

        .nm-help-icon {
            width: 42px;
            height: 42px;
            display: grid;
            place-items: center;
            margin-bottom: 16px;
            border-radius: 12px;
            color: var(--nm-green-dark);
            background: var(--nm-green-soft);
        }

        .nm-help-card h3 {
            margin: 0 0 7px;
            color: var(--nm-ink);
            font-size: 16px;
            line-height: 1.4;
        }

        .nm-help-card p {
            margin: 0;
            color: var(--nm-text);
            font-size: 13px;
            line-height: 1.7;
        }

        .nm-security {
            display: grid;
            grid-template-columns: auto 1fr;
            gap: 18px;
            margin-top: 58px;
            padding: 26px 28px;
            border: 1px solid var(--nm-warn-line);
            border-radius: 19px;
            background: var(--nm-warn);
        }

        .nm-security-icon {
            width: 46px;
            height: 46px;
            display: grid;
            place-items: center;
            border-radius: 13px;
            color: #8B5E00;
            background: rgba(255,255,255,.75);
        }

        .nm-security h2 {
            margin: 0 0 7px;
            color: #513B08;
            font-size: 19px;
        }

        .nm-security p {
            margin: 0;
            color: #725B25;
            font-size: 14px;
            line-height: 1.75;
        }

        .nm-contact-bottom {
            margin-top: 34px;
            padding: 31px;
            border-radius: 20px;
            background:
                linear-gradient(
                    135deg,
                    #EAFBF3,
                    #F6FCF9
                );
            border: 1px solid #D6F3E4;
            text-align: center;
        }

        .nm-contact-bottom h2 {
            margin: 0;
            color: var(--nm-ink);
            font-size: 23px;
        }

        .nm-contact-bottom p {
            max-width: 680px;
            margin: 10px auto 20px;
            color: var(--nm-text);
            font-size: 14px;
            line-height: 1.75;
        }

        @media (max-width: 1000px) {
            .nm-contact-info-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .nm-contact-form-map-grid {
                grid-template-columns: 1fr;
            }

            .nm-map-card {
                min-height: 470px;
            }
        }

        @media (max-width: 760px) {
            .nm-contact-hero {
                padding: 52px 0 54px;
            }

            .nm-contact-hero p {
                font-size: 15px;
            }

            .nm-contact-info-wrap {
                margin-top: -20px;
            }

            .nm-help-grid {
                grid-template-columns: 1fr 1fr;
            }

            .nm-form-card {
                padding: 22px 18px;
            }

            .nm-contact-main,
            .nm-help-section {
                padding-top: 48px;
            }
        }

        @media (max-width: 540px) {
            .nm-contact-shell {
                width: min(100% - 22px, 1180px);
            }

            .nm-contact-info-grid,
            .nm-help-grid {
                grid-template-columns: 1fr;
            }

            .nm-info-card {
                padding: 18px;
            }

            .nm-contact-actions {
                flex-direction: column;
            }

            .nm-contact-btn {
                width: 100%;
            }

            .nm-security {
                grid-template-columns: 1fr;
                padding: 22px;
            }

            .nm-contact-bottom {
                padding: 26px 18px;
            }
        }
    </style>


    <section class="nm-contact-hero">
        <div class="nm-contact-shell">
            <div class="nm-contact-hero-inner">

                <div class="nm-contact-eyebrow">
                    <span class="nm-contact-eyebrow-dot"></span>
                    {{ $siteBrand }} Customer Care
                </div>

                <h1>যোগাযোগ করুন</h1>

                <p>
                    অর্ডার, পণ্য, ডেলিভারি, পেমেন্ট, রিটার্ন
                    বা অন্য যেকোনো সহায়তার জন্য আমাদের সঙ্গে
                    যোগাযোগ করুন। প্রয়োজনীয় তথ্য দিয়ে যোগাযোগ
                    করলে আমরা দ্রুত বিষয়টি পর্যালোচনা করতে পারব।
                </p>

                <div class="nm-contact-actions">

                    @if($sitePhone)
                    <a
                        class="nm-contact-btn nm-contact-btn-primary"
                        href="tel:{{ $sitePhone }}">

                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2
                            19.79 19.79 0 0 1-8.63-3.07
                            19.5 19.5 0 0 1-6-6
                            19.79 19.79 0 0 1-3.07-8.67
                            A2 2 0 0 1 4.11 2h3
                            a2 2 0 0 1 2 1.72
                            12.84 12.84 0 0 0 .7 2.81
                            2 2 0 0 1-.45 2.11L8.09 9.91
                            a16 16 0 0 0 6 6l1.27-1.27
                            a2 2 0 0 1 2.11-.45
                            12.84 12.84 0 0 0 2.81.7
                            A2 2 0 0 1 22 16.92z"/>
                        </svg>

                        {{ $sitePhone }}
                    </a>
                    @endif

                    <!--email_off--><a
                        class="nm-contact-btn nm-contact-btn-secondary"
                        href="mailto:{{ $siteEmail }}">

                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12
                            c0 1.1-.9 2-2 2H4
                            c-1.1 0-2-.9-2-2V6
                            c0-1.1.9-2 2-2z"/>
                            <path d="m22 6-10 7L2 6"/>
                        </svg>

                        {{ $siteEmail }}
                    </a><!--/email_off-->

                </div>
            </div>
        </div>
    </section>


    <div class="nm-contact-info-wrap">
        <div class="nm-contact-shell">

            <div class="nm-contact-info-grid">

                <div class="nm-info-card">
                    <div class="nm-info-icon">
                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2
                            19.79 19.79 0 0 1-8.63-3.07
                            19.5 19.5 0 0 1-6-6
                            19.79 19.79 0 0 1-3.07-8.67
                            A2 2 0 0 1 4.11 2h3"/>
                        </svg>
                    </div>

                    <div class="nm-info-label">হেল্পলাইন</div>

                    <div class="nm-info-value">
                        <a href="tel:+8801850602003">
                            01850602003
                        </a>
                    </div>
                </div>


                <div class="nm-info-card">
                    <div class="nm-info-icon">
                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2">
                            <rect
                                x="2"
                                y="4"
                                width="20"
                                height="16"
                                rx="2"/>
                            <path d="m22 6-10 7L2 6"/>
                        </svg>
                    </div>

                    <div class="nm-info-label">ইমেইল</div>

                    <div class="nm-info-value">
                        <!--email_off--><a href="mailto:{{ $siteEmail }}">
                            {{ $siteEmail }}
                        </a><!--/email_off-->
                    </div>
                </div>


                <div class="nm-info-card">
                    <div class="nm-info-icon">
                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2">
                            <path d="M21 10c0 7-9 12-9 12
                            S3 17 3 10a9 9 0 1 1 18 0z"/>
                            <circle cx="12" cy="10" r="3"/>
                        </svg>
                    </div>

                    <div class="nm-info-label">অফিস</div>

                    <div class="nm-info-value">
                        Noyamela (Beside Primary School),
                        Dinajpur - Biral Rd, Biral 5210,
                        Dinajpur
                    </div>
                </div>


                <div class="nm-info-card">
                    <div class="nm-info-icon">
                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2">
                            <circle cx="12" cy="12" r="9"/>
                            <path d="M12 7v5l3 2"/>
                        </svg>
                    </div>

                    <div class="nm-info-label">সাপোর্ট সময়</div>

                    <div class="nm-info-value">
                        Saturday - Thursday<br>
                        9:00 AM - 8:00 PM<br>
                        Friday: Closed
                    </div>
                </div>

            </div>
        </div>
    </div>


    <main class="nm-contact-main">
        <div class="nm-contact-shell">

            <div class="nm-section-heading">
                <span class="nm-section-kicker">
                    Customer Support
                </span>

                <h2>
                    আপনার প্রশ্ন আমাদের জানান
                </h2>

                <p>
                    নিচের ফরমে প্রয়োজনীয় তথ্য দিন অথবা ম্যাপ
                    থেকে আমাদের অফিসের অবস্থান দেখুন।
                </p>
            </div>


            <div class="nm-contact-form-map-grid">

                <div class="nm-form-card">

                    <div class="nm-card-heading">
                        <h3>একটি বার্তা পাঠান</h3>

                        <p>
                            আপনার প্রশ্ন বা সমস্যাটি সংক্ষেপে
                            বিস্তারিত লিখুন।
                        </p>
                    </div>

                    

<div id="contact-form">

@if(session('contact_success'))

<div style="
    padding:32px 22px;
    border:1px solid #b9ebd1;
    border-radius:18px;
    background:linear-gradient(135deg,#eafbf3,#f8fffb);
    text-align:center;
">

    <div style="
        width:64px;
        height:64px;
        margin:0 auto 18px;
        border-radius:50%;
        display:flex;
        align-items:center;
        justify-content:center;
        background:#00C46A;
        color:#fff;
        font-size:32px;
        font-weight:bold;
        box-shadow:0 10px 25px rgba(0,196,106,.25);
    ">
        ✓
    </div>

    <h3 style="
        margin:0 0 10px;
        font-size:23px;
        color:#101828;
    ">
        আমাদের সঙ্গে যোগাযোগ করার জন্য ধন্যবাদ!
    </h3>

    <p style="
        max-width:500px;
        margin:0 auto;
        color:#475467;
        line-height:1.8;
        font-size:14px;
    ">
        {{ session('contact_success') }}
    </p>

    <a
        href="{{ url('/contact-us#contact-form') }}"
        style="
            display:inline-block;
            margin-top:20px;
            padding:11px 18px;
            border-radius:10px;
            background:#fff;
            border:1px solid #b9ebd1;
            color:#008C4B;
            font-weight:700;
            text-decoration:none;
        "
    >
        আরেকটি বার্তা পাঠান
    </a>

</div>

@else

@if(session('contact_error'))

<div style="
    margin-bottom:18px;
    padding:14px 16px;
    border:1px solid #fecaca;
    border-radius:10px;
    background:#fff5f5;
    color:#991b1b;
">
    {{ session('contact_error') }}
</div>

@endif


@if($errors->any())

<div style="
    margin-bottom:18px;
    padding:14px 16px;
    border:1px solid #fed7aa;
    border-radius:10px;
    background:#fffaf2;
    color:#9a3412;
">

    <strong>নিচের তথ্যগুলো ঠিক করুন:</strong>

    <ul style="margin:8px 0 0;padding-left:20px;">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>

</div>

@endif


<form
    method="POST"
    action="{{ route('contact.submit') }}"
    class="nm-real-contact-form"
>

    @csrf

    <div style="
        position:absolute;
        left:-9999px;
        width:1px;
        height:1px;
        overflow:hidden;
    ">
        <input
            type="text"
            name="website"
            tabindex="-1"
            autocomplete="off"
        >
    </div>

    <div>
        <label for="contact-name">আপনার নাম</label>

        <input
            id="contact-name"
            type="text"
            name="name"
            value="{{ old('name') }}"
            placeholder="আপনার নাম লিখুন"
            maxlength="100"
            required
        >
    </div>

    <div>
        <label for="contact-email">ইমেইল</label>

        <input
            id="contact-email"
            type="email"
            name="email"
            value="{{ old('email') }}"
            placeholder="আপনার ইমেইল ঠিকানা"
            maxlength="254"
            required
        >
    </div>

    <div>
        <label for="contact-subject">বিষয়</label>

        <input
            id="contact-subject"
            type="text"
            name="subject"
            value="{{ old('subject') }}"
            placeholder="কী বিষয়ে যোগাযোগ করছেন?"
            maxlength="150"
            required
        >
    </div>

    <div>
        <label for="contact-message">বার্তা</label>

        <textarea
            id="contact-message"
            name="message"
            placeholder="আপনার প্রশ্ন বা সমস্যাটি বিস্তারিত লিখুন..."
            maxlength="3000"
            required
        >{{ old('message') }}</textarea>
    </div>

    <button type="submit">
        Send Message
    </button>

</form>

@endif

</div>



                    <div class="nm-order-note">
                        <div>
                            <strong>
                                অর্ডার সংক্রান্ত সহায়তা?
                            </strong><br>

                            যোগাযোগের সময় আপনার অর্ডার নম্বর
                            এবং অর্ডারে ব্যবহৃত ফোন নম্বর
                            প্রস্তুত রাখুন।
                        </div>
                    </div>

                </div>


                @if ($siteMapEcode || $siteAddress)
                <div class="nm-map-card">

                    <div class="nm-map-top">
                        <h3>আমাদের অবস্থান</h3>

                        @if ($siteAddress)
                        <p>
                            {{ $siteAddress }}
                        </p>
                        @endif
                    </div>

                    @if ($siteMapEcode)
                    <div class="nm-map-frame">
                        @if(str_starts_with(trim($siteMapEcode), '<iframe'))
                            {!! $siteMapEcode !!}
                        @else
                            <iframe
                                src="{{ $siteMapEcode }}"
                                width="600"
                                height="450"
                                style="border:0;"
                                allowfullscreen
                                loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade"
                                title="{{ $siteBrand }} Location">
                            </iframe>
                        @endif
                    </div>
                    @endif

                </div>
                @endif

            </div>


            <section class="nm-help-section">

                <div class="nm-section-heading">
                    <span class="nm-section-kicker">
                        How We Can Help
                    </span>

                    <h2>
                        কীভাবে আমরা সহায়তা করতে পারি
                    </h2>

                    <p>
                        কেনাকাটার আগে বা পরে প্রয়োজন অনুযায়ী
                        আমাদের Customer Care-এর সঙ্গে যোগাযোগ
                        করতে পারেন।
                    </p>
                </div>


                <div class="nm-help-grid">

                    <div class="nm-help-card">
                        <div class="nm-help-icon">
                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2">
                                <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14
                                a2 2 0 0 0 2-2V6l-3-4z"/>
                                <path d="M3 6h18"/>
                                <path d="M16 10a4 4 0 0 1-8 0"/>
                            </svg>
                        </div>

                        <h3>অর্ডার সহায়তা</h3>

                        <p>
                            নতুন অর্ডার, অর্ডার নিশ্চিতকরণ,
                            Cash on Delivery এবং বর্তমান
                            অর্ডার স্ট্যাটাস।
                        </p>
                    </div>


                    <div class="nm-help-card">
                        <div class="nm-help-icon">
                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2">
                                <path d="M3 3h13v13H3z"/>
                                <path d="M16 8h4l2 3v5h-6z"/>
                                <circle cx="7" cy="18" r="2"/>
                                <circle cx="18" cy="18" r="2"/>
                            </svg>
                        </div>

                        <h3>ডেলিভারি সহায়তা</h3>

                        <p>
                            ডেলিভারি সময়, কুরিয়ার সংক্রান্ত
                            সমস্যা এবং অর্ডারের বর্তমান
                            অবস্থার তথ্য।
                        </p>
                    </div>


                    <div class="nm-help-card">
                        <div class="nm-help-icon">
                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2">
                                <circle cx="12" cy="12" r="9"/>
                                <path d="M12 16v-4"/>
                                <path d="M12 8h.01"/>
                            </svg>
                        </div>

                        <h3>পণ্যের তথ্য</h3>

                        <p>
                            পণ্যের বিস্তারিত, ব্যবহার,
                            প্রয়োজনীয় তথ্য এবং বর্তমান
                            মজুত সম্পর্কে জানতে।
                        </p>
                    </div>


                    <div class="nm-help-card">
                        <div class="nm-help-icon">
                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2">
                                <path d="M3 12a9 9 0 1 0 3-6.7"/>
                                <path d="M3 4v5h5"/>
                            </svg>
                        </div>

                        <h3>রিটার্ন ও পরিবর্তন</h3>

                        <p>
                            ভুল, ক্ষতিগ্রস্ত বা অসম্পূর্ণ
                            পণ্য এবং পণ্য পরিবর্তন বা
                            ফেরতের অনুরোধ।
                        </p>
                    </div>


                    <div class="nm-help-card">
                        <div class="nm-help-icon">
                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2">
                                <rect
                                    x="3"
                                    y="5"
                                    width="18"
                                    height="14"
                                    rx="2"/>
                                <path d="M3 10h18"/>
                            </svg>
                        </div>

                        <h3>রিফান্ড সহায়তা</h3>

                        <p>
                            প্রযোজ্য রিফান্ড সংক্রান্ত
                            প্রশ্ন, প্রক্রিয়া এবং প্রয়োজনীয়
                            আপডেট জানতে।
                        </p>
                    </div>


                    <div class="nm-help-card">
                        <div class="nm-help-icon">
                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2">
                                <path d="M21 15a4 4 0 0 1-4 4H8l-5 3
                                1.5-4A8 8 0 1 1 21 15z"/>
                            </svg>
                        </div>

                        <h3>মতামত ও অন্যান্য সহায়তা</h3>

                        <p>
                            অভিযোগ, পরামর্শ, মতামত,
                            ওয়ারেন্টি বা অন্য কোনো
                            Customer Care সহায়তা।
                        </p>
                    </div>

                </div>

            </section>


            <section class="nm-security">

                <div class="nm-security-icon">
                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7
                        c0 6 8 10 8 10z"/>
                        <path d="m9 12 2 2 4-4"/>
                    </svg>
                </div>

                <div>
                    <h2>
                        আপনার নিরাপত্তা আমাদের কাছে গুরুত্বপূর্ণ
                    </h2>

                    <p>
                        {{ $siteBrand }} কখনো ফোন, ইমেইল, বার্তা বা
                        যোগাযোগ ফরমের মাধ্যমে আপনার মোবাইল
                        ব্যাংকিং PIN, কার্ড PIN, OTP বা অন্য
                        কোনো গোপন নিরাপত্তা তথ্য চাইবে না।
                        কেউ {{ $siteBrand }}-এর পরিচয় ব্যবহার করে এমন
                        তথ্য চাইলে তা প্রদান করবেন না এবং
                        আমাদের অফিসিয়াল নম্বরে যোগাযোগ করুন।
                    </p>
                </div>

            </section>


            <section class="nm-contact-bottom">

                <h2>
                    আরও সহায়তা প্রয়োজন?
                </h2>

                <p>
                    দ্রুত সহায়তার জন্য আমাদের Customer Care-এ
                    ফোন করুন অথবা ইমেইলে বিস্তারিত লিখে পাঠান।
                </p>

                <div
                    class="nm-contact-actions"
                    style="justify-content:center;margin-top:0">

                    @if($sitePhone)
                    <a
                        class="nm-contact-btn nm-contact-btn-primary"
                        href="tel:{{ $sitePhone }}">
                        কল করুন
                    </a>
                    @endif

                    <!--email_off--><a
                        class="nm-contact-btn"
                        style="
                            background:#fff;
                            color:#101828;
                            border:1px solid #D7E8DE
                        "
                        href="mailto:{{ $siteEmail }}">
                        ইমেইল করুন
                    </a><!--/email_off-->

                </div>

            </section>

        </div>
    </main>

</div>

