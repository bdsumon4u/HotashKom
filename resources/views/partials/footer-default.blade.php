<footer class="site__footer">
    <div class="site-footer" style="background: #0b1320; color: #94a3b8; border-top: 1px solid rgba(255,255,255,0.08);">
        <!-- Trust Features Strip -->
        <div style="border-bottom: 1px solid rgba(255,255,255,0.06); padding: 16px 0; background: rgba(15, 23, 42, 0.6);">
            <div class="container">
                <div class="row">
                    <div class="col-6 col-md-3 mb-2 mb-md-0">
                        <div class="d-flex align-items-center">
                            <div class="d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; min-width: 40px; border-radius: 6px; background: rgba(var(--brand-rgb), 0.15); color: var(--brand); font-size: 18px; margin-right: 12px; flex-shrink: 0;">
                                <i class="fas fa-truck-fast"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 text-white font-weight-bold" style="font-size: 13.5px; line-height: 1.2;">Fast Delivery</h6>
                                <span style="font-size: 11.5px; color: #64748b;">Nationwide in 24-72h</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 mb-2 mb-md-0">
                        <div class="d-flex align-items-center">
                            <div class="d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; min-width: 40px; border-radius: 6px; background: rgba(var(--brand-rgb), 0.15); color: var(--brand); font-size: 18px; margin-right: 12px; flex-shrink: 0;">
                                <i class="fas fa-shield-halved"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 text-white font-weight-bold" style="font-size: 13.5px; line-height: 1.2;">100% Authentic</h6>
                                <span style="font-size: 11.5px; color: #64748b;">Genuine premium quality</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="d-flex align-items-center">
                            <div class="d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; min-width: 40px; border-radius: 6px; background: rgba(var(--brand-rgb), 0.15); color: var(--brand); font-size: 18px; margin-right: 12px; flex-shrink: 0;">
                                <i class="fas fa-hand-holding-dollar"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 text-white font-weight-bold" style="font-size: 13.5px; line-height: 1.2;">Cash on Delivery</h6>
                                <span style="font-size: 11.5px; color: #64748b;">Pay when received</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="d-flex align-items-center">
                            <div class="d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; min-width: 40px; border-radius: 6px; background: rgba(var(--brand-rgb), 0.15); color: var(--brand); font-size: 18px; margin-right: 12px; flex-shrink: 0;">
                                <i class="fas fa-rotate-left"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 text-white font-weight-bold" style="font-size: 13.5px; line-height: 1.2;">Easy Returns</h6>
                                <span style="font-size: 11.5px; color: #64748b;">Hassle-free 7 day policy</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container pt-3 pb-2">
            <div class="site-footer__widgets" style="padding: 0 !important;">
                <div class="row">
                    <div class="col-12 col-md-6 col-lg-4 mb-4 mb-lg-0">
                        <div class="site-footer__widget footer-contacts pr-lg-3">
                            <h5 class="footer-contacts__title text-white font-weight-bold mb-2" style="font-size: 16px; letter-spacing: -0.3px;">{{ $company->name ?? 'BagBazarBD' }}</h5>
                            <div class="footer-contacts__text mb-3" style="color: #94a3b8; font-size: 13px; line-height: 1.6;">
                                {{ $company->tagline ?? 'Your trusted online shopping destination for premium quality bags and lifestyle accessories with nationwide cash on delivery.' }}
                            </div>
                            <ul class="footer-contacts__contacts list-unstyled mb-0" style="color: #cbd5e1; font-size: 13.5px; line-height: 1.8;">
                                @if(!empty($company->address))
                                <li class="d-flex align-items-baseline mb-2">
                                    <i class="fas fa-location-dot mt-1" style="color: var(--brand); width: 16px; min-width: 16px; margin-right: 10px; flex-shrink: 0;"></i>
                                    <span>{{ $company->address }}</span>
                                </li>
                                @endif
                                @if(!empty($company->email))
                                <li class="d-flex align-items-center mb-2">
                                    <i class="far fa-envelope" style="color: var(--brand); width: 16px; min-width: 16px; margin-right: 10px; flex-shrink: 0;"></i>
                                    <a href="mailto:{{ $company->email }}" style="color: #cbd5e1; text-decoration: none;">{{ $company->email }}</a>
                                </li>
                                @endif
                                @if(!empty($company->phone))
                                <li class="d-flex align-items-center">
                                    <i class="fas fa-phone-volume" style="color: var(--brand); width: 16px; min-width: 16px; margin-right: 10px; flex-shrink: 0;"></i>
                                    <a href="tel:{{ $company->phone }}" class="text-white font-weight-bold" style="text-decoration: none; font-size: 14.5px;">{{ $company->phone }}</a>
                                </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                    @if($menuItems->isNotEmpty())
                    <div class="col-6 col-md-6 col-lg-2 mb-4 mb-lg-0">
                        <div class="site-footer__widget footer-links">
                            <h5 class="footer-links__title text-white font-weight-bold mb-2" style="font-size: 15px;">Quick Links</h5>
                            <ul class="footer-links__list list-unstyled mb-0" style="line-height: 2.0; font-size: 13.5px;">
                                @foreach($menuItems as $item)
                                @php
                                    $rawHref = $item->href;
                                    $isExternal = \Illuminate\Support\Str::startsWith($rawHref, ['http://', 'https://', 'mailto:', 'tel:', '#']);
                                    $href = $isExternal ? $rawHref : url($rawHref);
                                @endphp
                                <li class="footer-links__item">
                                    <a href="{{ $href }}" class="footer-links__link" style="color: #94a3b8; text-decoration: none; transition: color 0.2s;" onmouseover="this.style.color='var(--brand)'" onmouseout="this.style.color='#94a3b8'" @unless($isExternal) wire:navigate.hover @endunless>{{ $item->name }}</a>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    @endif
                    <div class="col-6 col-md-6 col-lg-3 mb-4 mb-lg-0">
                        <div class="site-footer__widget footer-links">
                            <h5 class="footer-links__title text-white font-weight-bold mb-2" style="font-size: 15px;">Customer Care</h5>
                            <ul class="footer-links__list list-unstyled mb-0" style="line-height: 2.0; font-size: 13.5px;">
                                <li><a href="{{ url('/about-us') }}" class="footer-links__link" style="color: #94a3b8; text-decoration: none; transition: color 0.2s;" onmouseover="this.style.color='var(--brand)'" onmouseout="this.style.color='#94a3b8'" wire:navigate.hover>About Us</a></li>
                                <li><a href="{{ url('/contact-us') }}" class="footer-links__link" style="color: #94a3b8; text-decoration: none; transition: color 0.2s;" onmouseover="this.style.color='var(--brand)'" onmouseout="this.style.color='#94a3b8'" wire:navigate.hover>Contact & Support</a></li>
                                <li><a href="{{ route('track-order') }}" class="footer-links__link" style="color: #94a3b8; text-decoration: none; transition: color 0.2s;" onmouseover="this.style.color='var(--brand)'" onmouseout="this.style.color='#94a3b8'" wire:navigate.hover>Track My Order</a></li>
                                <li><a href="{{ url('/return-and-refund-policy') }}" class="footer-links__link" style="color: #94a3b8; text-decoration: none; transition: color 0.2s;" onmouseover="this.style.color='var(--brand)'" onmouseout="this.style.color='#94a3b8'" wire:navigate.hover>Return & Refund Policy</a></li>
                                <li><a href="{{ url('/privacy-policy') }}" class="footer-links__link" style="color: #94a3b8; text-decoration: none; transition: color 0.2s;" onmouseover="this.style.color='var(--brand)'" onmouseout="this.style.color='#94a3b8'" wire:navigate.hover>Privacy Policy</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-3">
                        <div class="site-footer__widget footer-newsletter">
                            <h5 class="footer-newsletter__title text-white font-weight-bold mb-2" style="font-size: 15px;">Stay Connected</h5>
                            <div class="footer-newsletter__text mb-2" style="color: #94a3b8; font-size: 13px; line-height: 1.5;">Follow our official pages for newest arrivals, flash sales & exclusive vouchers:</div>
                            <ul class="footer-newsletter__social-links d-flex align-items-center list-unstyled mb-3" style="padding-left: 0;">
                                @if(!empty($company->phone))
                                <li class="footer-newsletter__social-link" style="margin-right: 8px;">
                                    <a href="tel:{{$company->phone}}" target="_blank" class="d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; border-radius: 4px; background: #1e293b; color: #ffffff; transition: all 0.2s ease; border: 1px solid rgba(255,255,255,0.06);" onmouseover="this.style.background='var(--brand)'; this.style.transform='translateY(-2px)';" onmouseout="this.style.background='#1e293b'; this.style.transform='none';" aria-label="Call us" data-contact-type="tel">
                                        <i class="fas fa-phone" style="font-size: 13px;"></i>
                                    </a>
                                </li>
                                @endif
                                @if(!empty($company->email))
                                <li class="footer-newsletter__social-link" style="margin-right: 8px;">
                                    <a href="mailto:{{$company->email}}" target="_blank" class="d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; border-radius: 4px; background: #1e293b; color: #ffffff; transition: all 0.2s ease; border: 1px solid rgba(255,255,255,0.06);" onmouseover="this.style.background='var(--brand)'; this.style.transform='translateY(-2px)';" onmouseout="this.style.background='#1e293b'; this.style.transform='none';" aria-label="Email us">
                                        <i class="fas fa-envelope" style="font-size: 13px;"></i>
                                    </a>
                                </li>
                                @endif
                                @foreach($social ?? [] as $item => $data)
                                @if(($link = $data->link ?? false) && $link != '#')
                                <li class="footer-newsletter__social-link" style="margin-right: 8px;">
                                    <a href="{{ url($link ?? '#') }}" target="_blank" class="d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; border-radius: 4px; background: #1e293b; color: #ffffff; transition: all 0.2s ease; border: 1px solid rgba(255,255,255,0.06);" onmouseover="this.style.background='var(--brand)'; this.style.transform='translateY(-2px)';" onmouseout="this.style.background='#1e293b'; this.style.transform='none';" aria-label="{{ ucfirst($item) }}">
                                        @switch($item)
                                        @case('facebook')
                                        <i class="fab fa-facebook-f" style="font-size: 13px;"></i>
                                        @break
                                        @case('twitter')
                                        <i class="fab fa-twitter" style="font-size: 13px;"></i>
                                        @break
                                        @case('instagram')
                                        <i class="fab fa-instagram" style="font-size: 13px;"></i>
                                        @break
                                        @case('youtube')
                                        <i class="fab fa-youtube" style="font-size: 13px;"></i>
                                        @break
                                        @default
                                        <i class="fas fa-link" style="font-size: 13px;"></i>
                                        @endswitch
                                    </a>
                                </li>
                                @endif
                                @endforeach
                            </ul>

                            <div style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.06); border-radius: 4px; padding: 8px 12px; font-size: 12.5px; color: #cbd5e1; display: inline-flex; align-items: center;">
                                <i class="fas fa-lock text-success" style="margin-right: 8px;"></i> 100% Safe & Secure Checkout
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <hr style="border-color: rgba(255,255,255,0.08); margin: 20px 0 16px;">

            <div class="site-footer__bottom d-flex flex-wrap align-items-center justify-content-between">
                <div class="site-footer__copyright" style="font-size: 13px; color: #64748b;">
                    &copy; {{ date('Y') }} <strong class="text-white">{{ $company->name ?? 'BagBazarBD' }}</strong>. All rights reserved.
                </div>
                @if(($name = $company->dev_name??'Hotash Tech') != '#' and ($link = $company->dev_link??'https://hotash.tech') != '#')
                <div class="site-footer__payments" style="color: #64748b; font-size: 12.5px;">
                    Developed By <a href="{{$company->dev_link??'https://hotash.tech'}}" class="text-white font-weight-bold" target="_blank">{{$company->dev_name??'Hotash Tech'}}</a>
                </div>
                @else
                <div class="site-footer__payments">
                    <img src="{{ asset('payments.png') }}" alt="Secure Payment Methods" style="height: 28px; max-width: 100%; object-fit: contain; opacity: 0.9;" />
                </div>
                @endif
            </div>
        </div>
    </div>
</footer>

