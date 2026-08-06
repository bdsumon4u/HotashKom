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
</div>
