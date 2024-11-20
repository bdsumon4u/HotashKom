<style>
    .contact .contact-info ul li {
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-align: center;
        -ms-flex-align: center;
        align-items: center;
        gap: 20px;
        margin-bottom: 30px;
    }
    .part-icon {
        text-align: center;
        width: 50px;
        height: 50px;
        line-height: 50px;
        background: #f3f3f3;
        border-radius: 50%;
        font-size: 18px;
        color: #fe5502;
    }
    .part-txt {
        width: calc(100% - 70px);
        margin-top: -6px;
        margin-bottom: -8px;
    }
    .contact .contact-form .map iframe {
        width: 100%;
        height: 450px;
    }
</style>
<div class="contact">
    @php $company = setting('company') @endphp
    <div class="row justify-content-between">
        <div class="col-xl-4 col-lg-5 col-md-6">
            <div class="contact-info">
                <h2 class="title"></h2>
                <ul class="list-unstyled">
                    <li>
                        <div class="part-icon">
                            <span><i class="fas fa-id-card"></i></span>
                        </div>
                        <div class="part-txt">
                            <div>{{ $company->contact_name ?? '' }}</div>
                            <a class="text-dark" href="mailto:{{$company->email}}">{{$company->email}}</a>
                            <br>
                            <a class="text-dark" href="tel:{{$company->phone}}">{{$company->phone}}</a>
                        </div>
                    </li>
                    <li>
                        <div class="part-icon">
                            <span><i class="fas fa-map-marker-alt"></i></span>
                        </div>
                        <div class="part-txt">{{$company->address}}</div>
                    </li>
                    <li>
                        <div class="part-icon">
                            <span><i class="fas fa-clock"></i></span>
                        </div>
                        <div class="part-txt">{{$company->office_time}}</div>
                    </li>
                </ul>
            </div>
        </div>
        <div class="col-xl-7 col-lg-7 col-md-6 p-0">
            <div class="contact-form">
                <div class="map">
                    {!! $company->gmap_ecode ?? '[CONTACT_FORM]' !!}
                </div>
            </div>
        </div>
    </div>
</div>