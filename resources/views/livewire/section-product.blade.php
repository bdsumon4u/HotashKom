<div class="shadow-sm card rounded-0">
    <div class="p-3 card-body">
        <div class="px-3 row">
            <div class="col-md-6 d-flex align-items-center">
                <strong>Manuallay add products begining of the section.</strong>
            </div>
            <input type="search" wire:model.live.debounce.250ms="search" id="search"
                placeholder="Search Product" class="col-md-6 form-control">
        </div>
        <div class="my-2 table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody wire:sortable="updateTaskOrder" wire:sortable.options="{ animation: 100 }">
                    @foreach ($products as $product)
                        @if(!isset($selectedProducts[$product['id']]))
                        <tr>
                            <td>
                                <img src="{{ asset($product['image']) }}" width="100"
                                    height="100" alt="">
                            </td>
                            <td>
                                <a class="mb-2 d-block"
                                    href="{{ route('products.show', $product['slug']) }}">{{ $product['name'] }}</a>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-primary"
                                    wire:click="addProduct({{ $product['id'] }})">Add</button>
                            </td>
                        </tr>
                        @endif
                    @endforeach
                    @forelse ($selectedProducts as $product)
                        <tr wire:sortable.item="{{ $product['id'] }}" wire:key="product-{{ $product['id'] }}">
                            <td>
                                <img src="{{ $product['image'] }}" width="100"
                                    height="100" alt="">
                                <input type="hidden" name="items[]" value="{{$product['id']}}">
                            </td>
                            <td>
                                <a
                                    href="{{ route('products.show', $product['slug']) }}">{{ $product['name'] }}</a>
                            </td>
                            <td>
                                <div class="d-flex">
                                    <button type="button" class="p-2 mr-1 btn btn-primary d-flex align-items-center justify-content-center" wire:sortable.handle>
                                        <i class="fa fa-arrows-alt"></i>
                                    </button>
                                    <button type="button" class="p-2 ml-1 btn btn-danger d-flex align-items-center justify-content-center" wire:click="removeProduct({{$product['id']}})">
                                        <i class="fa fa-times"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">No products selected.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>