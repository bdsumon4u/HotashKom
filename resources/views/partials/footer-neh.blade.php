<footer class="rankmet-footer">
    @php
        $desktopLogo = (isset($logo->desktop) && $logo->desktop) ? asset($logo->desktop) : null;
        $mobileLogo = (isset($logo->mobile) && $logo->mobile) ? asset($logo->mobile) : $desktopLogo;
        $hasPerfLogo = file_exists(public_path('performance/images/hk-logo-320.webp'));
        $perfLogo320 = asset('performance/images/hk-logo-320.webp');
        $perfLogo640 = asset('performance/images/hk-logo-640.webp');
        $siteBrand = $company->name ?? config('app.name');
    @endphp
    <div class="rankmet-footer-watermark d-none">
        @if($hasPerfLogo)
            <img
                src="{{ $perfLogo320 }}"
                srcset="{{ $perfLogo320 }} 320w, {{ $perfLogo640 }} 640w"
                sizes="255px"
                alt="{{ $siteBrand }}"
                width="320"
                height="81"
                loading="lazy"
                decoding="async"
            >
        @elseif($desktopLogo || $mobileLogo)
            <img
                src="{{ $desktopLogo ?: $mobileLogo }}"
                alt="{{ $siteBrand }}"
                width="320"
                height="81"
                loading="lazy"
                decoding="async"
            >
        @endif
    </div>

    <div class="rankmet-footer-container">
        <div class="rankmet-footer-row">
            <!-- Column 1: Logo & Description -->
            <div class="rankmet-footer-col rankmet-footer-col-1">
                <a href="{{ url('/') }}" class="rankmet-footer-logo">
                    @if($hasPerfLogo)
                        <img
                            src="{{ $perfLogo320 }}"
                            srcset="{{ $perfLogo320 }} 320w, {{ $perfLogo640 }} 640w"
                            sizes="255px"
                            alt="{{ $siteBrand }}"
                            width="320"
                            height="81"
                            loading="lazy"
                            decoding="async"
                        >
                    @elseif($desktopLogo || $mobileLogo)
                        <img
                            src="{{ $desktopLogo ?: $mobileLogo }}"
                            alt="{{ $siteBrand }}"
                            width="320"
                            height="81"
                            loading="lazy"
                            decoding="async"
                        >
                    @else
                        <h5 class="m-0 font-weight-bold text-white" style="font-family: 'Plus Jakarta Sans', sans-serif;">{{ $siteBrand }}</h5>
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
                                $rawHref = (string) $item->href;
                                $footerItemName = (string) $item->name;

                                // FOOTER TRACK ORDER TO BLOGS START
                                $footerItemPath = parse_url(
                                    $rawHref,
                                    PHP_URL_PATH
                                );

                                $footerItemPath = is_string($footerItemPath)
                                    ? trim($footerItemPath, '/')
                                    : trim($rawHref, '/');

                                if ($footerItemPath === 'track-order') {
                                    $rawHref = '/blogs';
                                    $footerItemName = 'Blogs';
                                }
                                // FOOTER TRACK ORDER TO BLOGS END
                                $isExternal = \Illuminate\Support\Str::startsWith($rawHref, ['http://', 'https://', 'mailto:', 'tel:', '#']);
                                $href = $isExternal ? $rawHref : url($rawHref);
                            @endphp
                            <li>
                                <a href="{{ $href }}" @unless($isExternal) @endunless>{{ $footerItemName }}</a>
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
                                <a href="{{ route('category.show', $category) }}">{{ $category->name }}</a>
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
                        <!--email_off--><a href="mailto:{{ $company->email }}" class="rankmet-footer-email"><i class="fa fa-envelope mr-2" style="color: var(--brand);"></i>{{ $company->email }}</a><!--/email_off-->
                    </div>
                @endif

                @if(isset($company->phone) && $company->phone)
                    <div class="rankmet-footer-phone-box">
                        <a href="tel:{{ $company->phone }}" class="rankmet-footer-phone"><i class="fa fa-phone-alt mr-2" style="color: var(--brand);"></i>{{ $company->phone }}</a>
                    </div>
                @endif

                @if(isset($company->address) && $company->address)
                    <div class="rankmet-footer-address-box">
                        <i class="fa fa-map-marker-alt" style="color: var(--brand); margin-top: 4px;"></i>
                        <span>{{ $company->address }}</span>
                    </div>
                @endif

                <ul class="rankmet-footer-list rankmet-footer-policies">
                    <!-- <li><a href="{{ url('privacy-policy') }}">Privacy Policy</a></li>
                    <li><a href="{{ url('refund-policy') }}">Refund Policy</a></li>
                    <li><a href="{{ url('terms-of-services') }}">Terms of Services</a></li> -->
                </ul>
            </div>
        </div>

        <!-- Bottom: Dynamic Credit & Checkout Bar -->
        <div class="rankmet-footer-bottom">
            <div class="rankmet-footer-copyright">
                &copy; {{ date('Y') }} {{ $company->name ?? '' }}. All Rights Reserved. | Trade Licence: 55271728022600104
            </div>
            <div class="rankmet-footer-credit"><i class="fa fa-truck mr-2" style="color: var(--brand);"></i>সারা বাংলাদেশে Cash on Delivery</div>
        </div>
    </div>
</footer>
