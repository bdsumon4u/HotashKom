<div class="tab-pane active" id="item-courier" role="tabpanel">
    <div class="form-row">
        @php($Pathao = optional($Pathao ?? null))
        <div class="form-group col-md-auto">
            <div class="checkbox checkbox-secondary">
                <input type="hidden" name="Pathao[enabled]" value="0">
                <x-checkbox id="Pathao" name="Pathao[enabled]" value="1" :checked="!!$Pathao->enabled" />
                <x-label for="Pathao" />
            </div>
        </div>
        <div class="form-group col-md-auto">
            <x-input name="Pathao[username]" :value="$Pathao->username" placeholder="Type Pathao username here" />
            <x-error field="Pathao[username]" />
        </div>
        <div class="form-group col-md-auto">
            <x-input type="password" name="Pathao[password]" :value="$Pathao->password"
                placeholder="Type Pathao password here" />
            <x-error field="Pathao[password]" />
        </div>
        <div class="form-group col-md-auto">
            <x-input name="Pathao[client_id]" :value="$Pathao->client_id" placeholder="Type API client_id here" />
            <x-error field="Pathao[client_id]" />
        </div>
        <div class="form-group col-md-auto">
            <x-input name="Pathao[client_secret]" :value="$Pathao->client_secret" placeholder="Type API client_secret here" />
            <x-error field="Pathao[client_secret]" />
        </div>
        <div class="form-group col-md-auto">
            <x-input name="Pathao[store_id]" :value="$Pathao->store_id" placeholder="Type store_id here" />
            <x-error field="Pathao[store_id]" />
        </div>
    </div>
    <div class="form-row">
        @php($SteadFast = optional($SteadFast ?? null))
        <div class="form-group col-md-auto">
            <div class="checkbox checkbox-secondary">
                <input type="hidden" name="SteadFast[enabled]" value="0">
                <x-checkbox id="SteadFast" name="SteadFast[enabled]" value="1" :checked="!!$SteadFast->enabled" />
                <x-label for="SteadFast" />
            </div>
        </div>
        <div class="form-group col-md-auto">
            <x-input name="SteadFast[key]" :value="$SteadFast->key" placeholder="Type API key here" />
            <x-error field="SteadFast[key]" />
        </div>
        <div class="form-group col-md-auto">
            <x-input name="SteadFast[secret]" :value="$SteadFast->secret" placeholder="Type API secret here" />
            <x-error field="SteadFast[secret]" />
        </div>
    </div>
</div>
