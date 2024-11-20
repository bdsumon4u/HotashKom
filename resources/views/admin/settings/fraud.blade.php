<div class="tab-pane active" id="item-fraud" role="tabpanel">
    <div class="row">
        <div class="col-md-3">
            <label for="fraud[allow_per_hour]">Allow orders per hour per IP</label>
            <x-input name="fraud[allow_per_hour]" :value="$fraud->allow_per_hour ?? 3" />
            <x-error field="fraud[allow_per_hour]" />
        </div>
        <div class="col-md-3">
            <label for="fraud[allow_per_day]">Allow orders per day per IP</label>
            <x-input name="fraud[allow_per_day]" :value="$fraud->allow_per_day ?? 7" />
            <x-error field="fraud[allow_per_day]" />
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="fraud[max_qty_per_product]">Max quantity each product</label>
                <x-input name="fraud[max_qty_per_product]" :value="$fraud->max_qty_per_product ?? 3" />
                <x-error field="fraud[max_qty_per_product]" />
            </div>
        </div>
    </div>
</div>
