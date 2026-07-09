<style>
    @import url('https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap');

    .rankmet-footer {
        position: relative;
        background-color: #1B1919;
        padding-top: 70px;
        padding-bottom: 20px;
        font-family: 'DM Sans', sans-serif;
        color: #FFFFFF;
        overflow: hidden;
        z-index: 1;
    }

    .rankmet-footer-watermark {
        position: absolute;
        bottom: -50px;
        left: 0;
        right: 0;
        width: 100%;
        z-index: -1;
        pointer-events: none;
        overflow: hidden;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .rankmet-footer-watermark img {
        max-height: 250px;
        width: auto;
        opacity: 0.05;
        transition: opacity 0.3s ease;
    }

    .rankmet-footer:hover .rankmet-footer-watermark img {
        opacity: 0.15;
    }

    .rankmet-footer-container {
        max-width: 1320px;
        margin: 0 auto;
        padding: 0 30px;
        position: relative;
        z-index: 2;
    }

    .rankmet-footer-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
        gap: 30px;
        padding-bottom: 50px;
    }

    .rankmet-footer-col {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
    }

    .rankmet-footer-col-1 {
        flex: 1 1 0px;
        max-width: 320px;
    }

    .rankmet-footer-col-2 {
        flex: 1 1 150px;
        max-width: 200px;
    }

    .rankmet-footer-col-3 {
        flex: 1 1 250px;
        max-width: 320px;
    }

    .rankmet-footer-col-4 {
        flex: 1 1 220px;
        max-width: 280px;
    }

    .rankmet-footer-logo {
        display: inline-block;
        margin-bottom: 25px;
        text-decoration: none;
    }

    .rankmet-footer-logo img {
        max-height: 50px;
        width: auto;
    }

    .rankmet-footer-desc {
        font-size: 17px;
        line-height: 1.625em;
        letter-spacing: -0.2px;
        color: #F6F4F1;
        margin: 0;
    }

    .rankmet-footer-title {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 19px;
        font-weight: 600;
        line-height: 1.2632em;
        color: #FF6600;
        margin-top: 0;
        margin-bottom: 25px;
        text-transform: none;
        letter-spacing: 0;
    }

    .rankmet-footer-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .rankmet-footer-list li {
        margin-bottom: 12px;
        line-height: 1.5;
    }

    .rankmet-footer-list li:last-child {
        margin-bottom: 0;
    }

    .rankmet-footer-list a {
        color: #FFFFFF;
        font-size: 16px;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .rankmet-footer-list a:hover {
        color: #FF6600;
    }

    .rankmet-footer-socials {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 25px;
    }

    .rankmet-footer-socials a {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        border: 1px solid #414141;
        display: flex;
        justify-content: center;
        align-items: center;
        color: #FFFFFF;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .rankmet-footer-socials a svg,
    .rankmet-footer-socials a i {
        width: 16px;
        height: 16px;
        font-size: 16px;
        display: flex;
        justify-content: center;
        align-items: center;
        fill: currentColor;
        transition: fill 0.3s ease;
    }

    .rankmet-footer-socials a:hover {
        background-color: #FF6600;
        border-color: #FF6600;
        color: #FFFFFF;
    }

    .rankmet-footer-email-box {
        margin-bottom: 20px;
    }

    .rankmet-footer-email {
        font-family: 'DM Sans', sans-serif;
        font-size: 16px;
        color: #FF6600;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .rankmet-footer-email:hover {
        color: #FFFFFF;
    }

    .rankmet-footer-policies li {
        margin-bottom: 8px;
    }

    .rankmet-footer-bottom {
        border-top: 1px solid rgba(255, 255, 255, 0.08);
        padding-top: 30px;
        padding-bottom: 20px;
        text-align: center;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        gap: 15px;
    }

    .rankmet-footer-checkout-img {
        max-width: 100%;
        height: auto;
        max-height: 44px;
    }

    .rankmet-footer-copyright {
        font-size: 14px;
        color: #B7B7B7;
    }

    .rankmet-footer-credit {
        font-size: 14px;
        color: #B7B7B7;
    }

    .rankmet-footer-credit a:hover {
        color: #FFFFFF !important;
    }

    /* Responsive adjustments */
    @media (max-width: 1024px) {
        .rankmet-footer {
            padding-top: 50px;
            padding-left: 30px;
            padding-right: 30px;
        }

        .rankmet-footer-row {
            flex-direction: column;
            align-items: center;
            text-align: center;
            gap: 40px;
            padding-bottom: 40px;
        }

        .rankmet-footer-col {
            align-items: center;
            width: 100%;
            max-width: 100% !important;
        }

        .rankmet-footer-title {
            margin-bottom: 15px;
        }

        .rankmet-footer-bottom {
            padding-top: 20px;
        }

        .rankmet-footer-watermark {
            bottom: -50px;
        }

        .rankmet-footer-socials {
            justify-content: center;
        }
    }

    @media (max-width: 480px) {
        .rankmet-footer {
            padding-left: 20px;
            padding-right: 20px;
        }
    }
</style>

<footer class="rankmet-footer">
    <div class="rankmet-footer-watermark">
        @if(isset($logo->desktop) && $logo->desktop)
            <img src="{{ asset($logo->desktop) }}" alt="">
        @elseif(isset($logo->mobile) && $logo->mobile)
            <img src="{{ asset($logo->mobile) }}" alt="">
        @endif
    </div>

    <div class="rankmet-footer-container">
        <div class="rankmet-footer-row">
            <!-- Column 1: Logo & Description -->
            <div class="rankmet-footer-col rankmet-footer-col-1">
                <a href="{{ url('/') }}" class="rankmet-footer-logo">
                    @if(isset($logo->desktop) && $logo->desktop)
                        <img src="{{ asset($logo->desktop) }}" alt="{{ $company->name ?? '' }}">
                    @elseif(isset($logo->mobile) && $logo->mobile)
                        <img src="{{ asset($logo->mobile) }}" alt="{{ $company->name ?? '' }}">
                    @else
                        <h5 class="text-white m-0 font-weight-bold" style="font-family: 'Plus Jakarta Sans', sans-serif;">{{ $company->name ?? '' }}</h5>
                    @endif
                </a>
                <p class="rankmet-footer-desc">
                    {{ $company->tagline ?? '' }}
                </p>
            </div>

            <!-- Column 2: Quick Links -->
            <div class="rankmet-footer-col rankmet-footer-col-2">
                <h6 class="rankmet-footer-title">Quick Links</h6>
                @if(isset($menuItems) && $menuItems->isNotEmpty())
                    <ul class="rankmet-footer-list">
                        @foreach($menuItems as $item)
                            @php
                                $rawHref = $item->href;
                                $isExternal = \Illuminate\Support\Str::startsWith($rawHref, ['http://', 'https://', 'mailto:', 'tel:', '#']);
                                $href = $isExternal ? $rawHref : url($rawHref);
                            @endphp
                            <li>
                                <a href="{{ $href }}" @unless($isExternal) wire:navigate.hover @endunless>{{ $item->name }}</a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <!-- Column 3: Services -->
            <div class="rankmet-footer-col rankmet-footer-col-3">
                <h6 class="rankmet-footer-title">Categories</h6>
                @if(isset($categories) && $categories->isNotEmpty())
                    <ul class="rankmet-footer-list">
                        @foreach($categories->shuffle()->take(6) as $category)
                            <li>
                                <a href="{{ route('categories.products', $category) }}">{{ $category->name }}</a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <!-- Column 4: Get in Touch -->
            <div class="rankmet-footer-col rankmet-footer-col-4">
                <h6 class="rankmet-footer-title">Get in Touch</h6>

                <div class="rankmet-footer-socials">
                    @foreach($social ?? [] as $item => $data)
                        @if(($link = $data->link ?? false) && $link != '#')
                            <a href="{{ url($link ?? '#') }}" target="_blank" aria-label="{{ ucfirst($item) }}">
                                @switch($item)
                                    @case('facebook')
                                        <svg xmlns="http://www.w3.org/2000/svg" width="8" height="15" viewBox="0 0 8 15"><path d="M4.40924 5.15357V3.27857C4.40946 3.15538 4.43254 3.03345 4.47714 2.91973C4.52174 2.80602 4.58699 2.70277 4.66917 2.61587C4.75135 2.52896 4.84884 2.46012 4.95607 2.41328C5.0633 2.36644 5.17818 2.34251 5.29412 2.34286H6.17563V1.41826e-07H4.41092C4.06345 -0.000117134 3.71938 0.0724976 3.39833 0.213697C3.07728 0.354896 2.78556 0.561913 2.53982 0.822924C2.29409 1.08393 2.09915 1.39382 1.96615 1.73489C1.83316 2.07596 1.76471 2.44153 1.76471 2.81071V5.15357H0V7.5H1.76471V15H4.41009V7.5H6.1748L7.05882 5.15357H4.40924Z"></path></svg>
                                        @break
                                    @case('linkedin')
                                        <svg aria-hidden="true" class="e-font-icon-svg e-fab-linkedin" viewBox="0 0 448 512" xmlns="http://www.w3.org/2000/svg"><path d="M416 32H31.9C14.3 32 0 46.5 0 64.3v383.4C0 465.5 14.3 480 31.9 480H416c17.6 0 32-14.5 32-32.3V64.3c0-17.8-14.4-32.3-32-32.3zM135.4 416H69V202.2h66.5V416zm-33.2-243c-21.3 0-38.5-17.3-38.5-38.5S80.9 96 102.2 96c21.2 0 38.5 17.3 38.5 38.5 0 21.3-17.2 38.5-38.5 38.5zm282.1 243h-66.4V312c0-24.8-.5-56.7-34.5-56.7-34.6 0-39.9 27-39.9 54.9V416h-66.4V202.2h63.7v29.2h.9c8.9-16.8 30.6-34.5 62.9-34.5 67.2 0 79.7 44.3 79.7 101.9V416z"></path></svg>
                                        @break
                                    @case('instagram')
                                        <svg aria-hidden="true" class="e-font-icon-svg e-fab-instagram" viewBox="0 0 448 512" xmlns="http://www.w3.org/2000/svg"><path d="M224.1 141c-63.6 0-114.9 51.3-114.9 114.9s51.3 114.9 114.9 114.9S339 319.5 339 255.9 287.7 141 224.1 141zm0 189.6c-41.1 0-74.7-33.5-74.7-74.7s33.5-74.7 74.7-74.7 74.7 33.5 74.7 74.7-33.6 74.7-74.7 74.7zm146.4-194.3c0 14.9-12 26.8-26.8 26.8-14.9 0-26.8-12-26.8-26.8s12-26.8 26.8-26.8 26.8 12 26.8 26.8zm76.1 27.2c-1.7-35.9-9.9-67.7-36.2-93.9-26.2-26.2-58-34.4-93.9-36.2-37-2.1-147.9-2.1-184.9 0-35.8 1.7-67.6 9.9-93.9 36.1s-34.4 58-36.2 93.9c-2.1 37-2.1 147.9 0 184.9 1.7 35.9 9.9 67.7 36.2 93.9s58 34.4 93.9 36.2c37 2.1 147.9 2.1 184.9 0 35.9-1.7 67.7-9.9 93.9-36.2 26.2-26.2 34.4-58 36.2-93.9 2.1-37 2.1-147.8 0-184.8zM398.8 388c-7.8 19.6-22.9 34.7-42.6 42.6-29.5 11.7-99.5 9-132.1 9s-102.7 2.6-132.1-9c-19.6-7.8-34.7-22.9-42.6-42.6-11.7-29.5-9-99.5-9-132.1s-2.6-102.7 9-132.1c7.8-19.6 22.9-34.7 42.6-42.6 29.5-11.7 99.5-9 132.1-9s102.7-2.6 132.1 9c19.6 7.8 34.7 22.9 42.6 42.6 11.7 29.5 9 99.5 9 132.1s2.7 102.7-9 132.1z"></path></svg>
                                        @break
                                    @case('pinterest')
                                        <svg aria-hidden="true" class="e-font-icon-svg e-fab-pinterest" viewBox="0 0 496 512" xmlns="http://www.w3.org/2000/svg"><path d="M496 256c0 137-111 248-248 248-25.6 0-50.2-3.9-73.4-11.1 10.1-16.5 25.2-43.5 30.8-65 3-11.6 15.4-59 15.4-59 8.1 15.4 31.7 28.5 56.8 28.5 74.8 0 128.7-68.8 128.7-154.3 0-81.9-66.9-143.2-152.9-143.2-107 0-163.9 71.8-163.9 150.1 0 36.4 19.4 81.7 50.3 96.1 4.7 2.2 7.2 1.2 8.3-3.3.8-3.4 5-20.3 6.9-28.1.6-2.5.3-4.7-1.7-7.1-10.1-12.5-18.3-35.3-18.3-56.6 0-54.7 41.4-107.6 112-107.6 60.9 0 103.6 41.5 103.6 100.9 0 67.1-33.9 113.6-78 113.6-24.3 0-42.6-20.1-36.7-44.8 7-29.5 20.5-61.3 20.5-82.6 0-19-10.2-34.9-31.4-34.9-24.9 0-44.9 25.7-44.9 60.2 0 22 7.4 36.8 7.4 36.8s-24.5 103.8-29 123.2c-5 21.4-3 51.6-.9 71.2C65.4 450.9 0 361.1 0 256 0 119 111 8 248 8s248 111 248 248z"></path></svg>
                                        @break
                                    @case('twitter')
                                        <i class="fab fa-twitter"></i>
                                        @break
                                    @case('youtube')
                                        <i class="fab fa-youtube"></i>
                                        @break
                                    @default
                                        <i class="fab fa-{{ $item }}"></i>
                                @endswitch
                            </a>
                        @endif
                    @endforeach
                </div>

                @if(isset($company->email) && $company->email)
                    <div class="rankmet-footer-email-box">
                        <a href="mailto:{{ $company->email }}" class="rankmet-footer-email">{{ $company->email }}</a>
                    </div>
                @endif

                <ul class="rankmet-footer-list rankmet-footer-policies">
                    <li><a href="{{ url('privacy-policy') }}">Privacy Policy</a></li>
                    <li><a href="{{ url('refund-policy') }}">Refund Policy</a></li>
                    <li><a href="{{ url('terms-of-services') }}">Terms of Services</a></li>
                </ul>
            </div>
        </div>

        <!-- Bottom: Dynamic Credit & Checkout Bar -->
        <div class="rankmet-footer-bottom">
            @if(isset($company->dev_name) && $company->dev_name != '#' && isset($company->dev_link) && $company->dev_link != '#')
                <div class="rankmet-footer-credit">
                    Developed By <a href="{{ $company->dev_link }}" target="_blank" style="color: #FF6600; text-decoration: none;">{{ $company->dev_name }}</a>
                </div>
            @else
                <img src="{{ asset('payments.png') }}" alt="Payment methods" class="rankmet-footer-checkout-img" style="height: 44px; width: auto;">
            @endif

            <div class="rankmet-footer-copyright">
                &copy; {{ date('Y') }} {{ $company->name ?? '' }}. All Rights Reserved.
            </div>
        </div>
    </div>
</footer>
