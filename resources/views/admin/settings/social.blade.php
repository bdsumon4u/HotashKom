<div class="tab-pane active" id="item-2" role="tabpanel">
    <div class="row">
        <div class="col-sm-12">
            <h4><small class="border-bottom mb-1">Social</small></h4>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fab fa-facebook"></i></span>
                </div>
                <x-input type="url" name="social[facebook][link]" :value="$social->facebook->link ?? ''" />
            </div>
        </div>
        <div class="col-md-6">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fab fa-twitter"></i></span>
                </div>
                <x-input type="url" name="social[twitter][link]" :value="$social->twitter->link ?? ''" />
            </div>
        </div>
        <div class="col-md-6">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fab fa-instagram"></i></span>
                </div>
                <x-input type="url" name="social[instagram][link]" :value="$social->instagram->link ?? ''" />
            </div>
        </div>
        <div class="col-md-6">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fab fa-youtube"></i></span>
                </div>
                <x-input type="url" name="social[youtube][link]" :value="$social->youtube->link ?? ''" />
            </div>
        </div>
        <div class="col-md-6">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fab fa-pinterest"></i></span>
                </div>
                <x-input type="url" name="social[pinterest][link]" :value="$social->pinterest->link ?? ''" />
            </div>
        </div>
        <div class="col-md-6">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fab fa-linkedin"></i></span>
                </div>
                <x-input type="url" name="social[linkedin][link]" :value="$social->linkedin->link ?? ''" />
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <label class="mb-1">Facebook Group Link</label>
            <div class="form-group">
                <x-input type="url" name="social[facebook_group][link]" :value="$social->facebook_group->link ?? ''" />
            </div>
        </div>
    </div>
</div>