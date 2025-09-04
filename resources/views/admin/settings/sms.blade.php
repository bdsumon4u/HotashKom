<div class="tab-pane active" id="item-sms" role="tabpanel">
    <div class="form-row">
        <div class="form-group col-md-auto">
            <div class="checkbox checkbox-secondary">
                <input type="hidden" name="ElitBuzz[enabled]" value="0">
                <x-checkbox id="ElitBuzz" name="ElitBuzz[enabled]" value="1"
                    :checked="!!($ElitBuzz->enabled ?? false)" />
                <x-label for="ElitBuzz" />
            </div>
        </div>
        <div class="form-group col-md-auto">
            <x-input name="ElitBuzz[api_key]" :value="$ElitBuzz->api_key ?? ''" placeholder="Type API key here" />
            <x-error field="ElitBuzz[api_key]" />
        </div>
        <div class="form-group col-md-auto">
            <x-input name="ElitBuzz[sender_id]" :value="$ElitBuzz->sender_id ?? ''"
                placeholder="Type API sender_id here" />
            <x-error field="ElitBuzz[sender_id]" />
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-md-auto">
            <div class="checkbox checkbox-secondary">
                <input type="hidden" name="BDWebs[enabled]" value="0">
                <x-checkbox id="BDWebs" name="BDWebs[enabled]" value="1"
                    :checked="!!($BDWebs->enabled ?? false)" />
                <x-label for="BDWebs" />
            </div>
        </div>
        <div class="form-group col-md-auto">
            <x-input name="BDWebs[api_key]" :value="$BDWebs->api_key ?? ''" placeholder="Type API key here" />
            <x-error field="BDWebs[api_key]" />
        </div>
        <div class="form-group col-md-auto">
            <x-input name="BDWebs[sender_id]" :value="$BDWebs->sender_id ?? ''"
                placeholder="Type API sender_id here" />
            <x-error field="BDWebs[sender_id]" />
        </div>
    </div>
    <div class="form-row d-none">
        <div class="col-md-auto">
            <div class="checkbox checkbox-secondary">
                <input type="hidden" name="show_option[admin_otp]" value="0">
                <x-checkbox id="admin-otp" name="show_option[admin_otp]" value="1"
                    :checked="!!($show_option->admin_otp ?? false)" />
                <label for="admin-otp">Require OTP for AdminPanel Login</label>
            </div>
        </div>
    </div>
    <div class="pt-2 mt-2 mb-4 form-row border-top border-bottom">
        <div class="col-md-10">
            <div class="form-group">
                <label for="">OTP Template</label>
                <x-textarea name="SMSTemplates[otp]" id="SMSTemplates[otp]">{{$SMSTemplates->otp ?? null}}</x-textarea>
                <x-error field="SMSTemplates[otp]" />
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label for="">Short Code(s):</label>
                <ul>
                    <li>
                        <small>OTP Code: <strong>[code]</strong></small>
                    </li>
                </ul>
            </div>
        </div>
        <div class="col-md-10">
            <div class="form-group">
                <label for="">Confirmation Template</label>
                <x-textarea name="SMSTemplates[confirmation]" id="SMSTemplates[confirmation]">{{$SMSTemplates->confirmation ?? null}}</x-textarea>
                <x-error field="SMSTemplates[confirmation]" />
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label for="">Short Code(s):</label>
                <ul>
                    <li>
                        <small>Order ID: <strong>[id]</strong></small>
                    </li>
                    <li>
                        <small>Customer Name: <strong>[name]</strong></small>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
