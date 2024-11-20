<div class="tab-pane active" id="item-3" role="tabpanel">
    <div class="row">
        <livewire:free-delivery :free-delivery="$free_delivery ?? null" :delivery-charge="$delivery_charge" />
        <div class="col-md-12">
            <div class="form-group">
                <label for="delivery-text">Delivery Text</label>
                <x-textarea editor name="delivery_text" id="delivery-text">{!! $delivery_text ?? '' !!}</x-textarea>
                <x-error field="delivery_text" />
            </div>
        </div>
    </div>
</div>

@push('js')
    <script src="{{ asset('js/tinymce.js') }}"></script>
@endpush
