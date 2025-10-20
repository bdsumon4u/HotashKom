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
                                <li><i class="footer-contacts__icon fas fa-globe-americas"></i> {{ $company->address ??
                                    '' }}</li>
                                <li><i class="footer-contacts__icon far fa-envelope"></i> {{ $company->email ?? '' }}
                                </li>
                                <li><i class="footer-contacts__icon fas fa-mobile-alt"></i> {{ $company->phone ?? '' }}
                                </li>
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
                            <div class="footer-newsletter__text footer-newsletter__text--social">Follow us on social
                                networks</div>
                            <ul class="footer-newsletter__social-links">
                                <li class="footer-newsletter__social-link footer-newsletter__social-link--phone">
                                    <a href="tel:{{$company->phone}}" target="_blank" class="bg-primary">
                                        <i class="fas fa-phone"></i>
                                    </a>
                                </li>
                                <li class="footer-newsletter__social-link footer-newsletter__social-link--phone">
                                    <a href="mailto:{{$company->email}}" target="_blank" class="bg-secondary">
                                        <i class="fas fa-envelope"></i>
                                    </a>
                                </li>
                                @foreach($social ?? [] as $item => $data)
                                @if(($link = $data->link ?? false) && $link != '#')
                                <li class="footer-newsletter__social-link footer-newsletter__social-link--{{ $item }}">
                                    <a href="{{ url($link ?? '#') }}" target="_blank">
                                        @switch($item)
                                        @case('facebook')
                                        <i class="fab fa-facebook-f"></i>
                                        @break
                                        @case('twitter')
                                        <i class="fab fa-twitter"></i>
                                        @break
                                        @case('instagram')
                                        <i class="fab fa-instagram"></i>
                                        @break
                                        @case('youtube')
                                        <i class="fab fa-youtube"></i>
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
                    &copy; {{ $company->name ?? '' }}
                </div>
                @if(($name = $company->dev_name??'Hotash Tech') != '#' and ($link =
                $company->dev_link??'https://hotash.tech') != '#')
                <div class="site-footer__payments">
                    Developed By <a href="{{$company->dev_link??'https://hotash.tech'}}"
                        class="text-danger">{{$company->dev_name??'Hotash Tech'}}</a>
                </div>
                @else
                <div class="site-footer__payments">
                    <img src="{{ asset('payments.webp') }}" style="height: 55px;" />
                </div>
                @endif
            </div>
        </div>
    </div>
</footer>
