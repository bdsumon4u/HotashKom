@push('styles')
<style>
    .block-slideshow{margin-bottom:20px;position:relative}.block-slideshow .owl-carousel .owl-dots{bottom:16px;position:absolute;background:#fff;display:-ms-flexbox;display:flex;padding:4px;border-radius:9px;left:50%;transform:translateX(-50%)}.block-slideshow .owl-carousel .owl-dot{width:10px;height:10px;border-radius:5px;background:#e0e0e0}.block-slideshow .owl-carousel .owl-dot:focus{outline:none}.block-slideshow .owl-carousel .owl-dot:hover{background:#d1d1d1}.block-slideshow .owl-carousel .owl-dot.active{background:var(--primary)}.block-slideshow .owl-carousel .owl-dot+.owl-dot{margin-left:6px}.block-slideshow__slide{position:relative;display:block;color:inherit}.block-slideshow__slide:hover{color:inherit}.block-slideshow__slide-image{position:absolute;left:0;right:0;width:100%;height:100%;background-repeat:no-repeat}.block-slideshow__slide-image--mobile{display:none}.block-slideshow__slide-content{position:absolute;bottom:46px;left:46px}.block-slideshow__slide-title{font-size:30px;line-height:34px;font-weight:700;margin-bottom:12px;opacity:0;transition:all 1s .2s}.block-slideshow__slide-text{line-height:1.625;opacity:0;transform:translateY(15px);transition:all .8s .5s}.block-slideshow__slide-button{margin-top:40px;opacity:0;transition:all 1s .4s}.block-slideshow .active .block-slideshow__slide-button,.block-slideshow .active .block-slideshow__slide-text,.block-slideshow .active .block-slideshow__slide-title{opacity:1;transform:none}.block-slideshow--layout--full{margin-top:20px}@media (min-width:768px){.block-slideshow--layout--full .block-slideshow__body,.block-slideshow--layout--full .block-slideshow__slide{height:440px}.block-slideshow--layout--full .block-slideshow__slide-content{bottom:54px;left:72px}.block-slideshow--layout--full .block-slideshow__slide-title{margin-bottom:16px;line-height:36px}.block-slideshow--layout--full .block-slideshow__slide-button{margin-top:48px}}@media (min-width:992px) and (max-width:1199px){.block-slideshow--layout--full .block-slideshow__slide-image--desktop{background-position:-70px top}.block-slideshow--layout--full .block-slideshow__slide-content{left:56px}}@media (min-width:768px) and (max-width:991px){.block-slideshow--layout--full .block-slideshow__slide-image--desktop{background-position:-190px top}.block-slideshow--layout--full .block-slideshow__slide-content{bottom:56px;left:48px}.block-slideshow--layout--full .block-slideshow__slide-title{margin-bottom:8px}.block-slideshow--layout--full .block-slideshow__slide-button{margin-top:40px}}.block-slideshow--layout--with-departments .block-slideshow__body{margin-top:15px;height:395px}.block-slideshow--layout--with-departments .block-slideshow__slide{height:395px}@media (min-width:992px){.block-slideshow--layout--with-departments .block-slideshow__body{margin-left:-15px}}@media (max-width:991px){.block-slideshow--layout--with-departments .block-slideshow__slide-button .btn{font-size:.875rem;height:calc(1.875rem + 2px);line-height:1.25;padding:.375rem 1rem;font-weight:500}.block-slideshow--layout--with-departments .block-slideshow__slide-button .btn.btn-svg-icon{width:calc(1.875rem + 2px)}}@media (max-width:767px){.block-slideshow__body,.block-slideshow__slide{height:395px}.block-slideshow__slide-image--mobile{background-position:top;display:block}.block-slideshow__slide-content{top:30px;text-align:center;left:5%;right:5%}.block-slideshow__slide-title{font-size:26px;line-height:32px}.block-slideshow__slide-text{display:none}.block-slideshow__slide-button{margin-top:24px}.block-slideshow__slide-button .btn{font-size:.875rem;height:calc(1.875rem + 2px);line-height:1.25;padding:.375rem 1rem;font-weight:500}.block-slideshow__slide-button .btn.btn-svg-icon{width:calc(1.875rem + 2px)}}
    .block-slideshow__body, .block-slideshow__slide {
        height: 440px !important;
    }
    .block-slideshow__slide-image--desktop {
        background-size: cover;
    }
    .block-slideshow__body .owl-carousel .owl-nav {
        /* position: absolute; */
        height: 100%;
        display: flex;
        width: 100%;
        justify-content: space-between;
        align-items: center;
        font-size: 40px;
        top: 0;
    }
    .block-slideshow__body .owl-carousel .owl-nav button {
        position: absolute;
        top: 35%;
        height: 60px;
        color: white;
        background: rgba(0, 0, 0, 0.1);
        padding-left: 5px !important;
        padding-right: 5px !important;
    }
    .owl-prev {
        left: 0;
    }
    .owl-next {
        right: 0;
    }
    .block-slideshow__body .owl-carousel .owl-nav button:focus {
        outline: none;
    }
    @media (max-width: 749px) {
        .block-slideshow {
            margin-bottom: 40px;
        }
        #slideshow-container {
            padding-left: 20px;
            padding-right: 20px;
        }
        .block-slideshow__body {
            margin-top: 5px !important;
            overflow: hidden;
            max-width: 100%;
        }
    }
    @media (max-width: 767px) {
        .block-slideshow__body, .block-slideshow__slide {
            height: 140px !important;
        }
        .block-slideshow__slide-image--mobile {
            background-size: cover;
        }
        .footer-contacts,
        .footer-links,
        .footer-newsletter {
            text-align: left;
        }
        .footer-links ul {
            padding-left: 27px;
        }
    }
</style>
@endpush
<div class="block block-slideshow -block-slideshow--layout--with-departments">
    <div id="slideshow-container" class="container" style="margin-left: 30px; margin-right: 30px;">
        <div class="block-slideshow__body">
            <div class="owl-carousel">
                @foreach(slides() as $slide)
                <a class="block-slideshow__slide" href="{{ $slide->btn_href ?? '#' }}">
                    <div class="block-slideshow__slide-image block-slideshow__slide-image--desktop"
                        style="background-image: url({{ asset($slide->desktop_src) }}); background-position: center;"></div>
                    <div class="block-slideshow__slide-image block-slideshow__slide-image--mobile"
                        style="background-image: url({{ asset($slide->mobile_src) }}); background-position: center;"></div>
                    <div class="block-slideshow__slide-content">
                        <div class="block-slideshow__slide-title">{!! $slide->title !!}</div>
                        <div class="block-slideshow__slide-text">{!! $slide->text !!}</div>
                        @if($slide->btn_href && $slide->btn_name)
                        <div class="block-slideshow__slide-button">
                            <span class="btn btn-primary btn-lg">{{ $slide->btn_name }}</span>
                        </div>
                        @endif
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </div>
</div>
