<div class="tab-pane active" id="item-1" role="tabpanel">
    <div class="row">
        <div class="col-sm-12">
            <h4><small class="border-bottom mb-1">Logo</small></h4>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <label for="desktop-logo" class="d-block">
                    <div>Desktop Logo ({{ config('services.logo.desktop.width', 260) }}x{{ config('services.logo.desktop.height', 54) }})</div>
                    <img src="{{ asset($logo->desktop ?? '') ?? '' }}" alt="desktop Logo" class="img-responsive d-block" height="{{ config('services.logo.desktop.height', 54) }}" style="@unless($logo->desktop ?? '') display:none; @endunless">
                </label>
                <input type="file" name="logo[desktop]" id="desktop-logo" class="form-control mb-1 @if($logo->desktop ?? '') d-none @endif">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="mobile-logo" class="d-block">
                    <div>Mobile Logo ({{ config('services.logo.mobile.width', 192) }}x{{ config('services.logo.mobile.height', 40) }})</div>
                    <img src="{{ asset($logo->mobile ?? '') ?? '' }}" alt="mobile Logo" class="img-responsiv d-blocke" height="{{ config('services.logo.mobile.height', 40) }}" style="@unless($logo->mobile ?? '') display:none; @endunless">
                </label>
                <input type="file" name="logo[mobile]" id="mobile-logo" class="form-control mb-1 @if($logo->mobile ?? '') d-none @endif">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="login-logo" class="d-block">
                    <div>Dashboard Logo ({{ config('services.logo.desktop.width', 192) }}x{{ config('services.logo.desktop.height', 40) }})</div>
                    <img src="{{ asset($logo->login ?? $logo->desktop ?? '') ?? '' }}" alt="login Logo" class="img-responsiv d-blocke" height="{{ config('services.logo.desktop.height', 40) }}" style="@unless($logo->desktop ?? '') display:none; @endunless">
                </label>
                <input type="file" name="logo[login]" id="login-logo" class="form-control mb-1 @if($logo->login ?? $logo->desktop ?? '') d-none @endif">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="favicon-logo" class="d-block">
                    <div>Favicon ({{ config('services.logo.favicon.width', 56) }}x{{ config('services.logo.favicon.height', 56) }})</div>
                    <img src="{{ asset($logo->favicon ?? '') ?? '' }}" alt="Favicon" class="img-responsive d-block" height="{{ config('services.logo.favicon.height', 56) }}" style="@unless($logo->favicon ?? '') display:none; @endunless">
                </label>
                <input type="file" name="logo[favicon]" id="favicon-logo" class="form-control mb-1 @if($logo->favicon ?? '') d-none @endif">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <h4><small class="border-bottom mb-1">Info</small></h4>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="company-name">Company Name</label>
                <x-input name="company[name]" id="company-name" :value="$company->name ?? ''" />
                <x-error field="company.name" />
            </div>
            <div class="form-group">
                <label for="company-email">Company Email</label>
                <x-input name="company[email]" id="company-email" :value="$company->email ?? ''" />
                <x-error field="company.email" />
            </div>
            <div class="form-group">
                <label for="company-phone">Company Phone</label>
                <x-input type="tel" name="company[phone]" id="company-phone" :value="$company->phone ?? ''" />
                <x-error field="company.phone" />
            </div>
            <div class="form-group">
                <label for="whatsapp-number">Whatsapp No.</label>
                <x-input type="tel" name="company[whatsapp]" id="whatsapp-number" :value="$company->whatsapp ?? ''" />
                <x-error field="company.whatsapp" />
            </div>
            <div class="form-group">
                <label for="">Call For Order (space separated)</label>
                <x-input type="tel" name="call_for_order" id="call_for_order" :value="$call_for_order ?? null" />
                <x-error field="call_for_order" />
            </div>
            <div class="form-group">
                <label for="">Messenger Chat Link</label>
                <x-input type="text" name="company[messenger]" id="messenger-chat-link" :value="$company->messenger ?? 'https://m.me/'" />
                <x-error field="company.messenger" />
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="company-contact_name">Contact Person Name</label>
                <x-input name="company[contact_name]" id="company-contact_name" :value="$company->contact_name ?? ''" />
                <x-error field="company.contact_name" />
            </div>
            <div class="form-group">
                <label for="company-tagline">Company Tagline</label>
                <x-input name="company[tagline]" id="company-tagline" :value="$company->tagline ?? ''" />
                <x-error field="company.tagline" />
            </div>
            <div class="form-group">
                <label for="company-address">Company Address</label>
                <x-textarea name="company[address]" id="company-address">{{ $company->address ?? '' }}</x-textarea>
                <x-error field="company.address" />
            </div>
            <div class="form-group">
                <label for="company-office_time">Office Time</label>
                <x-textarea name="company[office_time]" id="company-office_time">{{ $company->office_time ?? '' }}</x-textarea>
                <x-error field="company.office_time" />
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <label for="gmap-ecode">Google Map Embed Code</label>
                <x-input name="company[gmap_ecode]" id="gmap-ecode" :value="$company->gmap_ecode ?? null" />
                <x-error field="company[gmap_ecode]" />
            </div>
        </div>
        <div class="col-md-6 d-none">
            <div class="form-group">
                <label for="dev-name">Dev Name</label>
                <x-input name="company[dev_name]" id="dev-name" :value="$company->dev_name ?? null" />
                <x-error field="company[dev_name]" />
            </div>
        </div>
        <div class="col-md-6 d-none">
            <div class="form-group">
                <label for="dev-link">Dev Link</label>
                <x-input name="company[dev_link]" id="dev-link" :value="$company->dev_link ?? null" />
                <x-error field="company[dev_link]" />
            </div>
        </div>
    </div>
</div>

@include('admin.settings.social')

@push('scripts')
<script>
    $(document).ready(function() {
        $('input[type="file"]').change(function() {
            var $img = $(this).parent().find('img');
            if (this.files && this.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $img.attr('src', e.target.result);
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    });
</script>
@endpush