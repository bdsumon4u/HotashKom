@extends('layouts.yellow.master')

@section('title', 'Track Order')

@section('content')

@include('partials.page-header', [
    'paths' => [
        url('/') => 'Home',
    ],
    'active' => 'Track Order',
])

<div class="block">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-5 col-lg-6 col-md-8">
                <div class="card flex-grow-1 mb-0 mt-5">
                    <div class="card-body">
                        <div class="card-title text-center">
                            <h1>Track Order</h1>
                        </div>
                        <p class="mb-4 pt-2">You can always track your orders from here.</p>
                        <form action="{{ route('track-order') }}">
                            <div class="form-group">
                                <label for="order">Please enter your order ID</label>
                                <x-input id="order" name="order" placeholder="Example: 1234" />
                            </div>
                            <div class="pt-3">
                                <button type="submit" class="btn btn-primary btn-lg btn-block">Track</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
