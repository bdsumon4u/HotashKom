<table class="table table-bordered table-hover">
    <tbody>
        @foreach ($products as $product)
            <tr>
                <td>
                    <img src="{{ asset(optional($product->base_image)->src) }}" width="100" height="100"
                        alt="">
                </td>
                <td>
                    <a href="{{ route('products.show', $product->slug) }}">{{ $product->var_name }}</a>
                    @php
                        $optionGroup = $product->variations
                            ->pluck('options')
                            ->flatten()
                            ->unique('id')
                            ->groupBy('attribute_id');
                        $attributes = \App\Models\Attribute::find($optionGroup->keys());
                    @endphp

                    @foreach ($attributes as $attribute)
                        <div class="form-group product__option">
                            <label class="product__option-label">{{ $attribute->name }}</label>
                            @if (strtolower($attribute->name) == 'color')
                                <div class="input-radio-color">
                                    <div class="input-radio-color__list">
                                        {{-- <label class="input-radio-color__item input-radio-color__item--white" style="color: #fff;" data-toggle="tooltip" title="" data-original-title="White">
                                    <input type="radio" name="color">
                                    <span></span>
                                </label> --}}
                                        @foreach ($optionGroup[$attribute->id] as $option)
                                            <label
                                                class="input-radio-color__item @if (strtolower($option->name) == 'white') input-radio-color__item--white @endif"
                                                style="color: {{ $option->value }};" data-toggle="tooltip"
                                                title="" data-original-title="{{ $option->name }}">
                                                <input type="radio" name="options[{{ $attribute->id }}]"
                                                    value="{{ $option->id }}" class="option-picker">
                                                <span></span>
                                            </label>
                                        @endforeach
                                        {{-- <label class="input-radio-color__item input-radio-color__item--disabled" style="color: #4080ff;" data-toggle="tooltip" title="" data-original-title="Blue">
                                    <input type="radio" name="color" disabled="disabled">
                                    <span></span>
                                </label> --}}
                                    </div>
                                </div>
                            @else
                                <div class="input-radio-label">
                                    <div class="input-radio-label__list">
                                        @foreach ($optionGroup[$attribute->id] as $option)
                                            <label>
                                                <input type="radio" name="options[{{ $attribute->id }}]"
                                                    value="{{ $option->id }}" class="option-picker">
                                                <span>{{ $option->name }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </td>
                <td>
                    <button class="btn btn-sm btn-primary">Add</button>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
