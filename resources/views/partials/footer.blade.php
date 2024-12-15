<footer class="site__footer">
    <div class="site-footer">
        <div class="container">
            <div class="site-footer__widgets">
                <div class="row justify-content-between">
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="site-footer__widget footer-contacts">
                            <h5 class="footer-contacts__title">{{ $company->name ?? '' }}</h5>
                            <div class="footer-contacts__text">{{ $company->tagline ?? '' }}</div>
                            <ul class="footer-contacts__contacts">
                                <li><i class="footer-contacts__icon fa fas fa-globe"></i> {{ $company->address ?? '' }}</li>
                                <li><i class="footer-contacts__icon fa far fa-envelope"></i> {{ $company->email ?? '' }}</li>
                                <li><i class="footer-contacts__icon fa fas fa-phone"></i> {{ $company->phone ?? '' }}</li>
                            </ul>
                        </div>
                    </div>
                    @if($menuItems->isNotEmpty())
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="site-footer__widget footer-links">
                            <h5 class="footer-links__title">Quick Links</h5>
                            <ul class="footer-links__list">
                                @foreach($menuItems as $item)
                                <li class="footer-links__item">
                                    <a href="{{ url($item->href) }}" class="footer-links__link">{{ $item->name }}</a>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    @endif
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="site-footer__widget footer-newsletter">
                            <h5 class="footer-newsletter__title">Socials</h5>
                            <div class="footer-newsletter__text footer-newsletter__text--social">Follow us on social networks</div>
                            <ul class="footer-newsletter__social-links">
                                <li class="footer-newsletter__social-link footer-newsletter__social-link--phone">
                                    <a href="tel:{{$company->phone}}" target="_blank" class="bg-primary">
                                        <i class="fa fas fa-phone"></i>
                                    </a>
                                </li>
                                <li class="footer-newsletter__social-link footer-newsletter__social-link--phone">
                                    <a href="mailto:{{$company->email}}" target="_blank" class="bg-secondary">
                                        <i class="fa fas fa-envelope"></i>
                                    </a>
                                </li>
                                @foreach($social ?? [] as $item => $data)
                                    @if(($link = $data->link ?? false) && $link != '#')
                                    <li class="footer-newsletter__social-link footer-newsletter__social-link--{{ $item }}">
                                        <a href="{{ url($link ?? '#') }}" target="_blank" @if($item == 'tiktok') style="background-color: black" @endif>
                                            @switch($item)
                                                @case('facebook')
                                                <i class="fa fab fa-facebook-f"></i>
                                                @break
                                                @case('twitter')
                                                <i class="fa fab fa-twitter"></i>
                                                @break
                                                @case('instagram')
                                                <i class="fa fab fa-instagram"></i>
                                                @break
                                                @case('youtube')
                                                <i class="fa fab fa-youtube"></i>
                                                @break
                                                @case('tiktok')
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-tiktok" viewBox="0 0 16 16">
                                                <path d="M9 0h1.98c.144.715.54 1.617 1.235 2.512C12.895 3.389 13.797 4 15 4v2c-1.753 0-3.07-.814-4-1.829V11a5 5 0 1 1-5-5v2a3 3 0 1 0 3 3z"/>
                                                </svg>
                                                @break
                                            @endswitch
                                        </a>
                                    </li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="site-footer__bottom">
                <div class="site-footer__copyright">
                    Copyright 2020 - {{ date('Y') }} &copy; {{ $company->name ?? '' }}
                </div>
                <div class="site-footer__payments d-none">
                    Developed By <a href="{{$company->dev_link??'https://cyber32.com'}}" class="text-danger">{{$company->dev_name??'Cyber 32'}}</a>
                </div>
            </div>
        </div>
    </div>
</footer>