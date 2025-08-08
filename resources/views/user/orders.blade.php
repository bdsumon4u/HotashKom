@extends('layouts.yellow.master')

@title('Edit Profile')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            @include('user.layouts.sidebar')
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Order History</h5>
                            @if(isOninda() && config('app.resell'))
                                <a href="{{ route('reseller.dashboard') }}" class="btn btn-success btn-sm">Reseller Panel</a>
                            @endif
                        </div>
                    </div>
                    <div class="card-divider"></div>
                    <div class="card-table">
                        <div class="table-responsive-sm">
                            <table>
                                <thead>
                                <tr>
                                    <th>Order</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Subtotal</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($orders as $order)
                                <tr>
                                    <td><a href="{{ route('track-order', ['order' => $order->id]) }}">#{{ $order->id }}</a></td>
                                    <td>{{ $order->created_at->format('d-M-Y') }}</td>
                                    <td>{{ $order->status }}</td>
                                    <td>{!! theMoney($order->data['subtotal']) !!}</td>
                                </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-divider"></div>
                    <div class="card-footer">
                        {{ $orders->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .pagination {
            justify-content: center;
        }
    </style>
@endpush
