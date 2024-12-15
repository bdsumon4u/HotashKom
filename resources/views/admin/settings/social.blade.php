<div class="tab-pane active" id="item-2" role="tabpanel">
    <div class="row">
        <div class="col-sm-12">
            <h4><small class="mb-1 border-bottom">Social</small></h4>
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
        <div class="col-md-6">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-tiktok" viewBox="0 0 16 16">
                        <path d="M9 0h1.98c.144.715.54 1.617 1.235 2.512C12.895 3.389 13.797 4 15 4v2c-1.753 0-3.07-.814-4-1.829V11a5 5 0 1 1-5-5v2a3 3 0 1 0 3 3z"/>
                        </svg>
                    </span>
                </div>
                <x-input type="url" name="social[tiktok][link]" :value="$social->tiktok->link ?? ''" />
            </div>
        </div>
    </div>
</div>