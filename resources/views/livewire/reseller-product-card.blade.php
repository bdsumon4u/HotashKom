<div>
    @if(isOninda() && (!auth('user')->user() || !auth('user')->user()->is_verified))
        <!-- Unverified User Notice -->
        <div class="p-3 text-center border rounded">
            <i class="mb-2 fa fa-lock fa-2x text-warning"></i>
            <h6 class="text-warning">Account Verification Required</h6>
            <p class="small text-muted mb-2">You need to verify your account to view prices and place orders.</p>
            <a href="{{ route('user.payment.verification') }}" class="btn btn-warning btn-sm">
                <i class="mr-1 fa fa-credit-card"></i>Verify Account
            </a>
        </div>
    @else
        @if($product->variations->isNotEmpty())
            <div class="mb-3">
                <button type="button" class="btn btn-outline-info btn-sm btn-block" wire:click="toggleOptions">
                    <i class="mr-1 fa fa-cog"></i>
                    {{ $showOptions ? 'Hide' : 'Show' }} Options
                </button>
            </div>

            @if($showOptions)
                <div class="p-2 mb-3 rounded border">
                    @foreach($attributes as $attribute)
                        <div class="mb-2">
                            <label class="small text-muted">{{ $attribute->name }}:</label>
                            <div class="flex-wrap gap-1 d-flex">
                                @foreach($optionGroup[$attribute->id] ?? [] as $option)
                                    <label class="btn btn-sm btn-outline-secondary">
                                        <input type="radio" wire:model.live="options.{{ $attribute->id }}"
                                               value="{{ $option->id }}" class="d-none">
                                        <span class="{{ $options[$attribute->id] == $option->id ? 'text-primary fw-bold' : '' }}">
                                            {{ $option->name }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        @endif

        <div class="mb-3">
            <label class="small text-muted">Quantity:</label>
            <div class="input-number product__quantity d-inline-block">
                <input type="number" class="form-control form-control-sm input-number__input"
                       wire:model.live="quantity" min="1" max="{{ $maxQuantity }}" readonly>
                <div class="input-number__add" wire:click="increment"></div>
                <div class="input-number__sub" wire:click="decrement"></div>
            </div>
        </div>

        <div class="mb-3">
            <label class="small text-muted">Your Selling Price (৳):</label>
            <div class="input-group input-group-sm">
                <input type="number" class="form-control form-control-sm"
                       wire:model.live.debounce.500ms="retailPrice" min="0" step="0.01"
                       @focus="$event.target.select()" required>
                <div class="input-group-append">
                    <span class="input-group-text">৳</span>
                </div>
            </div>
            <small class="text-muted">
                Suggested: <strong>{{ $product->suggestedRetailPrice() }}</strong>
            </small>
            @error('retailPrice')
                <small class="text-danger d-block">{{ $message }}</small>
            @enderror
        </div>

        <button type="button" class="btn btn-primary btn-block" wire:click="addToCart" wire:loading.attr="disabled">
            <span wire:loading.remove>
                <i class="mr-1 fa fa-cart-plus"></i>Add to Cart
            </span>
            <span wire:loading>
                <i class="mr-1 fa fa-spinner fa-spin"></i>Adding...
            </span>
        </button>
    @endif
</div>
