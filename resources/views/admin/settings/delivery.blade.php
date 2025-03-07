<div class="tab-pane active" id="item-3" role="tabpanel">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex">
                {{-- Enable Productwise Delivery Charge --}}
                <div class="ml-3 checkbox checkbox-secondary">
                    <input type="hidden" name="show_option[productwise_delivery_charge]" value="0">
                    <x-checkbox id="productwise-delivery-charge" name="show_option[productwise_delivery_charge]"
                        value="1" :checked="!!($show_option->productwise_delivery_charge ?? false)" />
                    <label for="productwise-delivery-charge" class="my-1">Productwise Delivery Charge</label>
                </div>

                {{-- Enable Quantitywise Delivery Charge --}}
                <div class="ml-3 checkbox checkbox-secondary">
                    <input type="hidden" name="show_option[quantitywise_delivery_charge]" value="0">
                    <x-checkbox id="quantitywise-delivery-charge" name="show_option[quantitywise_delivery_charge]"
                        value="1" :checked="!!($show_option->quantitywise_delivery_charge ?? false)" />
                    <label for="quantitywise-delivery-charge" class="my-1">Quantitywise Delivery Charge</label>
                </div>
            </div>
        </div>
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
