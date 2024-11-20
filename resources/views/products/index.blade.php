@extends('layouts.yellow.master')

@section('title', 'Products')

@section('content')

@include('partials.page-header', [
    'paths' => [
        url('/') => 'Home',
    ],
    'active' => 'Products',
    'page_title' => 'Products'
])

<div class="block">
    <div class="products-view">
        <div class="container">
            <div class="row">
                <div class="col">
                    <div class="products-view__options">
                        <div class="view-options">
                            <div class="view-options__legend">
                                @if(request('search'))
                                Found {{ $products->total() }} result(s) for "{{ request('search', 'NULL') }}"
                                @elseif($category = request()->route()->parameter('category'))
                                Showing from "{{ $category->name }}" category.
                                @elseif($brand = request()->route()->parameter('brand'))
                                Showing from "{{ $brand->name }}" brand.
                                @else
                                Showing {{ $products->count() }} of {{ $products->total() }} products
                                @endif
                            </div>
                            <div class="view-options__divider"></div>
                            <!-- <div class="view-options__control">
                                <label for="">Sort By</label>
                                <div>
                                    <select class="form-control form-control-sm" name="" id="">
                                        <option value="">Default</option>
                                        <option value="">Name (A-Z)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="view-options__control">
                                <label for="">Show</label>
                                <div>
                                    <select class="form-control form-control-sm" name="" id="">
                                        <option value="">15</option>
                                        <option value="">20</option>
                                        <option value="">25</option>
                                        <option value="">30</option>
                                    </select>
                                </div>
                            </div> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('partials.products.pure-grid', [
            'title' => null
        ])
        <div class="container">
            <div class="row">
                <div class="col">
                    <div class="products-view__pagination pt-0">
                        <!-- <ul class="pagination justify-content-center">
                            <li class="page-item disabled">
                                <a class="page-link page-link--with-arrow" href="#" aria-label="Previous">
                                    <svg class="page-link__arrow page-link__arrow--left" aria-hidden="true" width="8px" height="13px">
                                        <use xlink:href="{{ asset('strokya/images/sprite.svg#arrow-rounded-left-8x13') }}"></use>
                                    </svg>
                                </a>
                            </li>
                            <li class="page-item"><a class="page-link" href="#">1</a></li>
                            <li class="page-item active"><a class="page-link" href="#">2 <span class="sr-only">(current)</span></a></li>
                            <li class="page-item"><a class="page-link" href="#">3</a></li>
                            <li class="page-item">
                                <a class="page-link page-link--with-arrow" href="#" aria-label="Next">
                                    <svg class="page-link__arrow page-link__arrow--right" aria-hidden="true" width="8px" height="13px">
                                        <use xlink:href="{{ asset('strokya/images/sprite.svg#arrow-rounded-right-8x13') }}"></use>
                                    </svg>
                                </a>
                            </li>
                        </ul> -->
                        {!! $products->links() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection