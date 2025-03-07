@extends('layouts.light.master')
@section('title', 'Carts')

@section('breadcrumb-title')
    <h3>Carts</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">Carts</li>
@endsection

@section('content')
<div class="mb-5 row">
    <div class="col-sm-12">
        <div class="shadow-sm card rounded-0">
            <div class="p-3 card-header">
                <div class="px-3 row justify-content-between align-items-center">
                    <div><strong class="text-danger">এরা প্রোডাক্ট কার্টে এড করেছে কিন্তু অর্ডার করেনি।</strong></div>
                </div>
            </div>
            <div class="p-3 card-body">
                <div class="table-responive">
                    <table class="table table-bordered table-striped table-hover datatable" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Products</th>
                                <th>Last Update</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($carts as $cart)
                            <tr class="border border-danger">
                                <td>
                                    <div><a style="display: flex; column-gap: 5px;" href="{{ route('admin.orders.index', ['status' => '', 'phone' => $cart->phone]) }}" target="_blank">{{ $cart->name }} <i style="width: 15px;" data-feather="link"> </i></a></div>
                                    <div class="mt-2"><a href="tel:{{ $cart->phone }}">{{ $cart->phone }}</a></div>
                                </td>
                                <td>
                                    @foreach(unserialize($cart->content) as $product)
                                    <div class="dropcart__product d-flex" data-id="{{$product->id}}" style="gap: .5rem;">
                                        <div class="dropcart__product-image">
                                            <a href="{{route('products.show', $product->options->slug)}}" target="_blank">
                                                <img src="{{asset($product->options->image)}}" alt="" width="50" height="50">
                                            </a>
                                        </div>
                                        <div class="dropcart__product-info">
                                            <div class="dropcart__product-name">
                                                <a href="{{route('products.show', $product->options->slug)}}">{{$product->name}}</a>
                                            </div>
                                            <div class="dropcart__product-meta">
                                                <span class="dropcart__product-quantity">{{$product->qty}}</span> x <span class="dropcart__product-price">TK {{$product->price}}</span>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </td>
                                <td>
                                    {{\Illuminate\Support\Carbon::parse($cart->updated_at)->diffForHumans()}}
                                </td>
                                <td width="50">
                                    <x-form action="{{ route('admin.carts.destroy', $cart->identifier) }}" method="delete">
                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                    </x-form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection