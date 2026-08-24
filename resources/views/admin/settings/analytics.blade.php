<div class="tab-pane active" id="item-analytics" role="tabpanel">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="gtm-id">Google Tag Manager ID</label>
                <x-input name="gtm_id" id="gtm_id" :value="$gtm_id ?? null" />
                <x-error field="gtm_id" />
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="pixel-ids">Pixel IDs (space separated)</label>
                <x-textarea name="pixel_ids" id="pixel-ids">{{$pixel_ids ?? null}}</x-textarea>
                <x-error field="pixel_ids" />
            </div>
        </div>
    </div>
    @if(config('meta-pixel.meta_pixel') === true)
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label for="meta_pixel">Meta Pixel CAPI (Conversions API) Credentials</label>
                <x-textarea name="meta_pixel" id="meta_pixel">{{ $meta_pixel ?? null }}</x-textarea>
                <small class="form-text text-muted">
                    Format: <code>pixel_id:access_token:test_event_code</code>. Multiple pixels separated by <code>|</code> or newlines. If empty, falls back to META_PIXEL in .env.
                </small>
                <x-error field="meta_pixel" />
            </div>
        </div>
    </div>
    @endif
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label for="scripts">Custom Scripts</label>
                <x-textarea name="scripts" id="scripts">{{ $scripts ?? null }}</x-textarea>
                <x-error field="scripts" />
            </div>
        </div>
    </div>
    <hr>
    <h6 class="mb-3">Google Merchant &amp; Customer Reviews</h6>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="gcr_merchant_id">Google Customer Reviews Merchant ID</label>
                <x-input name="gcr_merchant_id" id="gcr_merchant_id" :value="$gcr_merchant_id ?? null" placeholder="e.g. 5825413307" />
                <x-error field="gcr_merchant_id" />
                <small class="form-text text-muted">Numeric ID from Google Merchant Center. Leave blank to disable the reviews widget.</small>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="google_merchant_id_prefix">Google Merchant Feed Item ID Prefix</label>
                <x-input name="google_merchant_id_prefix" id="google_merchant_id_prefix" :value="$google_merchant_id_prefix ?? 'hk-'" placeholder="e.g. hk-" />
                <x-error field="google_merchant_id_prefix" />
                <small class="form-text text-muted">Prefix prepended to product IDs in Google Merchant XML feed (e.g. <code>hk-</code>).</small>
            </div>
        </div>
    </div>
</div>
