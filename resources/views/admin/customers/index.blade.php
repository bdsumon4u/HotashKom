@extends('layouts.light.master')
@section('title', 'Customers')

@section('breadcrumb-title')
<h3>Customers</h3>
@endsection

@section('breadcrumb-items')
<li class="breadcrumb-item">Customers</li>
@endsection

@section('content')
<div class="row mb-5">
    <div class="col-sm-12">
        <div class="orders-table">
            <div class="card rounded-0 shadow-sm">
                <div class="card-header p-3">
                    <strong>All</strong>&nbsp;<small>Customers</small>
                </div>
                <div class="card-body p-3">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover datatable" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>S.I.</th>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Email</th>
                                    <th>Orders</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($customers as $customer)
                                <tr data-row-id="{{ $customer->id }}">
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $customer->id }}</td>
                                    <td>
                                        <a href="{{ route('admin.orders.index', ['user_id' => $customer->id, 'status' => '']) }}">{{ $customer->name }}</a>
                                    </td>
                                    <td>{{ $customer->phone_number }}</td>
                                    <td>{{ $customer->email }}</td>
                                    <td>{{ $customer->orders_count }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-danger">No customers found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="mt-3">
                            {{ $customers->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection