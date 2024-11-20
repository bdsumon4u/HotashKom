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
                    <span class="input-group-text"><i class="fa fa-facebook"></i></span>
                </div>
                <x-input type="url" name="social[facebook][link]" :value="$social->facebook->link ?? ''" />
            </div>
        </div>
        <div class="col-md-6">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fa fa-twitter"></i></span>
                </div>
                <x-input type="url" name="social[twitter][link]" :value="$social->twitter->link ?? ''" />
            </div>
        </div>
        <div class="col-md-6">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fa fa-instagram"></i></span>
                </div>
                <x-input type="url" name="social[instagram][link]" :value="$social->instagram->link ?? ''" />
            </div>
        </div>
        <div class="col-md-6">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fa fa-youtube"></i></span>
                </div>
                <x-input type="url" name="social[youtube][link]" :value="$social->youtube->link ?? ''" />
            </div>
        </div>
    </div>
</div>