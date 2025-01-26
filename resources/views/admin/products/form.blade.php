<div class="row">
    <div class="col-sm-12">
        <h4><small class="mb-1 border-bottom">General</small></h4>
    </div>
    <div class="col-sm-12">
        <div class="form-group">
            <x-label for="name" /><span class="text-danger">*</span>
            <x-input name="name" :value="$product->name" data-target="#slug" />
            <x-error field="name" />
        </div>
    </div>
    <div class="col-sm-12">
        <div class="form-group">
            <x-label for="slug" /><span class="text-danger">*</span>
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">{{ url('/product') }}/</div>
                </div>
                <x-input name="slug" :value="$product->slug" />
                <button class="input-group-append align-items-center btn btn-secondary" type="button" onclick="window.open('{{url('/products').'/'}}'+this.previousElementSibling.value, '_blank')">VISIT</button>
            </div>
            <x-error field="slug" />
        </div>
    </div>
    <div class="col-sm-12">
        <div class="form-group">
            <x-label for="description" /><span class="text-danger">*</span>
            <x-textarea editor name="description" cols="30" rows="10">{!! $product->description !!}</x-textarea>
            <x-error field="description" />
        </div>
    </div>
    <div class="col-md-6">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <x-label for="categories" /><span class="text-danger">*</span>
                    <x-category-dropdown :categories="$categories" name="categories[]" placeholder="Select Category" id="categories" multiple="true" :selected="old('categories', $product->categories->pluck('id')->toArray())" />
                    <x-error field="categories" class="d-block" />
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <x-label for="brand" />
                    <x-category-dropdown :categories="$brands" name="brand" placeholder="Select Brand" id="brand" :selected="old('brand', $product->brand_id)" />
                    <x-error field="brand" class="d-block" />
                </div>
            </div>
            <div class="col-sm-12">
                <h4><small class="mb-1 border-bottom">Inventory</small></h4>
            </div>
            <div class="col-sm-12">
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="hidden" name="should_track" value="0" />
                        <x-checkbox name="should_track" value="1" :checked="!!$product->should_track" class="should_track custom-control-input" />
                        <x-label for="should_track" class="custom-control-label" />
                        <x-error field="should_track" />
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="sku">Product Code</label><span class="text-danger">*</span>
                    <x-input name="sku" :value="$product->sku ?? genSKU()" />
                    <x-error field="sku" />
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group stock-count" @if(!old('should_track', $product->should_track)) style="display: none;" @endif>
                    <x-label for="stock_count" /><span class="text-danger">*</span>
                    <x-input name="stock_count" :value="$product->stock_count" />
                    <x-error field="stock_count" />
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="price">Old Price</label><span class="text-danger">*</span>
                    <x-input name="price" :value="$product->price" />
                    <x-error field="price" />
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="selling_price">New Price</label><span class="text-danger">*</span>
                    <x-input name="selling_price" :value="$product->selling_price" />
                    <x-error field="selling_price" />
                </div>
            </div>
        </div>
        <div class="shadow-sm card rounded-0">
            <div class="p-1 card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <strong>Wholesale (Quantity|Price)</strong>
                    <button type="button" class="btn btn-primary btn-sm add-wholesale">
                        <i class="fa fa-plus"></i>
                    </button>
                </div>
            </div>
            <div class="p-1 card-body">
                @foreach (old('wholesale.price', $product->wholesale['price'] ?? []) as $price)
                    <div class="mb-1 form-group">
                        <div class="input-group">
                            <x-input name="wholesale[quantity][]" placeholder="Quantity" value="{{old('wholesale.quantity', $product->wholesale['quantity'] ?? [])[$loop->index]}}" />
                            <x-input name="wholesale[price][]" placeholder="Price" value="{{$price}}" />
                            <div class="input-group-append">
                                <button type="button" class="btn btn-danger btn-sm remove-wholesale">
                                    <i class="fa fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
                <ul>
                    @foreach ([$errors->first('wholesale.price.*'), $errors->first('wholesale.quantity.*')] as $error)
                        <li class="text-danger">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    <div class="col-sm-12">
        <div class="row">
            <div class="col-md-6">
                <div class="row">
                    <div class="col-sm-12">
                        <h4><small class="mb-1 border-bottom">Delivery Charge</small></h4>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="shipping_inside">Inside Dhaka</label>
                            <x-input name="shipping_inside" :value="$product->shipping_inside ?? setting('delivery_charge')->inside_dhaka" />
                            <x-error field="shipping_inside" />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="shipping_outside">Outside Dhaka</label>
                            <x-input name="shipping_outside" :value="$product->shipping_outside ?? setting('delivery_charge')->outside_dhaka" />
                            <x-error field="shipping_outside" />
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <h4><small class="mb-1 border-bottom">Delivery and Return Policy</small></h4>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <x-textarea editor name="delivery_text">{{old('delivery_text', $product->delivery_text ?? setting('delivery_text'))}}</x-textarea>
                            <x-error field="delivery_text" />
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <div class="checkbox checkbox-secondary">
                                <x-checkbox name="is_active" value="1" :checked="!!$product->is_active" />
                                <x-label for="is_active" />
                                <x-error field="is_active" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="row">
                    <div class="col-sm-12">
                        <h4><small class="mb-1 border-bottom">Product Images</small></h4>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <!-- Button to Open the Modal -->
                            <label for="base_image" class="mb-0 d-block">
                                <strong>Base Image</strong>
                                <button type="button" class="px-2 btn single btn-light" data-toggle="modal" data-target="#single-picker" style="background: transparent; margin-left: 5px;">
                                    <i class="mr-1 fa fa-image text-secondary"></i>
                                    <span>Browse</span>
                                </button>
                            </label>
                            <div id="preview-{{optional($product->base_image)->id}}" class="base_image-preview @unless(old('base_image', optional($product->base_image)->id)) d-none @endunless" style="height: 150px; width: 150px; margin: 5px; margin-left: 0px;">
                                <img src="{{ old('base_image_src', asset(optional($product->base_image)->src)) }}" alt="Base Image" data-toggle="modal" data-target="#single-picker" id="base_image-preview" class="img-thumbnail img-responsive" style="display: {{ old('base_image_src', optional($product->base_image)->src) ? '' : 'none' }};">
                                <input type="hidden" name="base_image_src" value="{{ old('base_image_src', asset(optional($product->base_image)->src)) }}">
                                <input type="hidden" name="base_image" value="{{ old('base_image', optional($product->base_image)->id) }}" id="base-image" class="form-control">
                            </div>
                            @error('base_image')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="additional_images" class="mb-0 d-block">
                                <strong>Additional Images</strong>
                                <button type="button" class="px-2 btn multiple btn-light" data-toggle="modal" data-target="#multi-picker" style="background: transparent; margin-left: 5px;">
                                    <i class="mr-1 fa fa-image text-secondary"></i>
                                    <span>Browse</span>
                                </button>
                            </label>
                            <ul id="sortable" class="flex-wrap additional_images-previews d-flex" style="margin-left: -5px;">
                                @php
                                    $ids = old('additional_images', $product->additional_images->pluck('id')->toArray());
                                    $srcs = old('additional_images_srcs', $product->additional_images->pluck('src')->toArray());
                                @endphp
                                @foreach($srcs as $src)
                                    <li id="preview-{{$ids[$loop->index]}}" class="additional_images-preview position-relative" style="height: 150px; width: 150px; margin: 5px;">
                                        <i class="fa fa-times text-danger position-absolute" style="font-size: large; top: 0; right: 0; background: #ddd; padding: 2px; border-radius: 3px; cursor: pointer;" onclick="this.parentNode.remove()"></i>
                                        <img src="{{ $src }}" alt="Additional Image" data-toggle="modal" data-target="#multi-picker" id="additional_image-preview" class="img-thumbnail img-responsive">
                                        <input type="hidden" name="additional_images[]" value="{{ $ids[$loop->index] }}" style="margin: 5px;">
                                        <input type="hidden" name="additional_images_srcs[]" value="{{ $src }}" style="margin: 5px;">
                                    </li>
                                @endforeach
                            </ul>
                            <div class="clearfix"></div>
                            @error('additional_images')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-group" x-data="{desc_img: {{old('desc_img', $product->desc_img ?? 0)}}}">
                            <div class="checkbox d-inline checkbox-primary">
                                <input type="hidden" name="desc_img" value="0">
                                <x-checkbox name="desc_img" x-model="desc_img" value="1" />
                                <label for="desc_img">Show Images in Description</label>
                                <x-error field="desc_img" />
                            </div>
                            <div x-show="desc_img" class="form-control @error('desc_img_pos') is-invalid @enderror">
                                @foreach (['before_content' => 'Before Content', 'after_content' => 'After Content'] as $key => $option)
                                    <div class="custom-control custom-radio custom-control-inline">
                                        <input type="radio"
                                            name="desc_img_pos"
                                            class="custom-control-input"
                                            id="{{ $key }}"
                                            value="{{ $key }}"
                                            {{ $key == old('desc_img_pos', $product->desc_img_pos ?? 'after_content') ? 'checked' : '' }}>
                                        <label class="custom-control-label"
                                            for="{{ $key }}">{{ $option }}</label>
                                    </div>
                                @endforeach
                            </div>
                            <x-error field="desc_img_pos" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-12">
        <button type="submit" class="btn btn-success">Save Product</button>
    </div>
</div>
