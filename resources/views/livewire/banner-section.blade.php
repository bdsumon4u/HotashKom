<div class="row">
    <div class="col-md-12">
        @foreach($errors->all() as $error)
            <div class="alert alert-danger">{{ $error }}</div>
        @endforeach
    </div>
    @foreach ($columns as $i => $column)
    <div class="col-md-{{$column['width']}}">
        <div class="row">
            <div class="col-md-12">
                @if ($column['image'])
                <div class="form-group position-relative">
                    <img src="{{$column['image']}}" alt="Image" style="max-width: 100%">
                    <button type="button" class="position-absolute btn btn-sm btn-danger" style="top: 0; right: 0;" wire:click="removeColumn({{$i}})">X</button>
                </div>
                @else
                <div class="form-group">
                    <label for="image" class="mb-0 d-block">
                        <button type="button" class="px-2 btn single btn-light" data-toggle="modal" data-target="#single-picker" style="background: transparent; margin-left: 5px;">
                            <i class="mr-1 fa fa-image text-secondary"></i>
                            <span>Browse</span>
                        </button>
                    </label>
                    <div id="preview" class="base_image-preview" style="width: 100%; margin: 5px; margin-left: 0px;">
                        <img src="" alt="Image" data-toggle="modal" data-target="#single-picker" id="image-preview" class="img-thumbnail img-responsive">
                        <input type="hidden" name="data[columns][image_src][]" value="">
                        <input type="hidden" name="data[columns][image][]" value="" id="base-image" class="form-control">
                    </div>
                    @error('image')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                @endif
            </div>
            @php($width = $column['width'] < 6 ? 6 : 3)
            <div class="col-md-{{$width}}">
                <div class="form-group">
                    <label for="banner-width">Width <small>Total of 12</small></label>
                    <x-input name="data[columns][width][]" wire:model="columns.{{$i}}.width" id="banner-width" placeholder="Total of 12" />
                    <x-error field="data[columns][width][]" />
                </div>
            </div>
            <div class="col-md-{{$width}}">
                <div class="form-group">
                    <label for="banner-animation">Animation</label>
                    <select name="data[columns][animation][]" class="form-control" wire:model="columns.{{$i}}.animation" id="banner-animation">
                        @foreach (['fade-left', 'fade-right', 'fade-up', 'fade-down'] as $animation)
                            <option value="{{$animation}}">{{$animation}}</option>
                        @endforeach 
                    </select>
                    <x-error field="animation" />
                </div>
            </div>
            <div class="col-md-{{$width}}">
                <div class="form-group">
                    <label for="banner-link">Link</label>
                    <x-input name="data[columns][link][]" wire:model="columns.{{$i}}.link" id="banner-link" />
                    <x-error field="data[columns][link][]" />
                </div>
            </div>
            <div class="col-md-{{$width}}">
                <div class="form-group">
                    <label for="banner-categories">Categories <small>(<strong>Ctrl+Click</strong> for Multiple)</small></label>
                    <x-category-dropdown :categories="$categories" name="data[columns][categories][]" placeholder="Select Categories" id="banner-categories" multiple="true" :selected="old('categories')" />
                    <x-error field="data[columns][categories][]" class="d-block" />
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>