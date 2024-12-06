<div class="row">
    <div class="col-md-12">
        @foreach($errors->all() as $error)
            <div class="alert alert-danger">{{ $error }}</div>
        @endforeach
    </div>
    @foreach ($columns as $i => $column)
    <div class="col-md-{{$column['width']}} border border-bottom border-2 py-2 my-2">
        <div class="row">
            <div class="col-md-12">
                @if ($column['image'])
                <div class="form-group position-relative">
                    <img src="{{asset($column['image'])}}" alt="Image" style="max-width: 100%;">
                    <input type="hidden" name="data[columns][image_src][]" value="{{$column['image']}}">
                    <input type="hidden" name="data[columns][image][]" value="{{$column['image']}}" id="base-image-{{$i}}" class="form-control">
                    <button type="button" class="position-absolute btn btn-sm btn-danger" style="top: 0; right: 0;" wire:click="removeColumn({{$i}})">X</button>
                </div>
                @else
                <div class="form-group">
                    <label for="image" class="mb-0 d-block">
                        <button type="button" class="px-2 btn single btn-light" data-toggle="modal" data-target="#single-picker" style="background: transparent; margin-left: 5px;">
                            <i class="mr-1 fa fa-image text-secondary"></i>
                            <span>Browse</span>
                        </button>
                        <button type="button" class="btn btn-sm btn-danger" style="top: 0; right: 0;" wire:click="removeColumn({{$i}})">Remove</button>
                    </label>
                    <div id="preview-{{$i}}" class="base_image-preview" style="width: 100%; margin: 5px; margin-left: 0px;">
                        <img src="" alt="Image" data-toggle="modal" data-target="#single-picker" id="image-preview-{{$i}}" class="img-thumbnail img-responsive d-none">
                        <input type="hidden" name="data[columns][image_src][]" value="{{$column['image']}}">
                        <input type="hidden" name="data[columns][image][]" value="{{$column['image']}}" id="base-image-{{$i}}" class="form-control">
                    </div>
                    @error('image')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                @endif
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="banner-width-{{$i}}">Width <small>Total of 12</small></label>
                    <x-input name="data[columns][width][]" wire:model="columns.{{$i}}.width" value="{{ old('data.columns.width.'.$i) }}" id="banner-width-{{$i}}" placeholder="Total of 12" />
                    <x-error field="data[columns][width][]" />
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="banner-animation-{{$i}}">Animation</label>
                    <select name="data[columns][animation][]" wire:model="columns.{{$i}}.animation" class="form-control" value="{{ old('data.columns.animation.'.$i) }}" id="banner-animation-{{$i}}">
                        @foreach (['fade-left', 'fade-right', 'fade-up', 'fade-down'] as $animation)
                            <option value="{{$animation}}">{{$animation}}</option>
                        @endforeach 
                    </select>
                    <x-error field="animation" />
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="banner-link-{{$i}}">Link</label>
                    <x-input name="data[columns][link][]" wire:model="columns.{{$i}}.link" value="{{ old('data.columns.link.'.$i) }}" id="banner-link-{{$i}}" />
                    <x-error field="data[columns][link][]" />
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="banner-categories-{{$i}}">Categories <small>(<strong>Ctrl+Click</strong> for Multiple)</small></label>
                    <x-category-dropdown :categories="$categories" name="data[columns][categories][{{$i}}][]" placeholder="Select Categories" id="banner-categories-{{$i}}" multiple="true" :selected="old('data.columns.categories.'.$i, $column['categories'] ?? [])" />
                    <x-error field="data[columns][categories][{{$i}}][]" class="d-block" />
                </div>
            </div>
        </div>
    </div>
    @endforeach

    @if(!(end($columns) ?: ['image' => null])['image'] == null)
    <div class="col-md-12">
        <button type="button" class="btn btn-primary w-100" wire:click="addColumn">Add Item</button>
    </div>
    @endif
</div>