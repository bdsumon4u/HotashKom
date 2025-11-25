@extends('layouts.light.master')
@section('title', 'Leads')

@section('breadcrumb-title')
<h3>Leads</h3>
@endsection

@section('breadcrumb-items')
<li class="breadcrumb-item">Leads</li>
@endsection

@section('content')
<div class="mb-5 row">
    <div class="col-sm-12">
        <div class="shadow-sm card rounded-0">
            <div class="gap-2 card-header d-flex flex-column flex-md-row align-items-md-center justify-content-between">
                <div>
                    <strong>Lead</strong><small>Submissions</small>
                </div>
                <form action="{{ route('admin.leads.index') }}" method="GET" class="w-100 w-md-auto">
                    <div class="input-group">
                        <input type="text" name="search" value="{{ $search }}" class="form-control" placeholder="Search name, shop, email or phone">
                        <button class="btn btn-primary" type="submit">Search</button>
                    </div>
                </form>
            </div>
            <div class="p-3 card-body">
                @if (session('status'))
                <div class="mb-3 alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
                @endif
                <div class="table-responsive">
                    <table class="table align-middle table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Shop</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Message</th>
                                <th>Submitted At</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($leads as $lead)
                            <tr>
                                <td>{{ $leads->firstItem() + $loop->index }}</td>
                                <td>{{ $lead->name }}</td>
                                <td>{{ $lead->shop_name ?? '—' }}</td>
                                <td>{{ $lead->email ?? '—' }}</td>
                                <td>{{ $lead->phone }}</td>
                                <td style="max-width: 320px;">{{ $lead->message }}</td>
                                <td>{{ $lead->created_at->format('d M Y h:i A') }}</td>
                                <td class="text-center">
                                    <form action="{{ route('admin.leads.destroy', $lead) }}" method="POST" onsubmit="return confirm('Delete this lead?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center text-danger">No leads found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $leads->links() }}
                </div>
            </div>
            <div class="p-3 card-footer">
                Lead Form: <a href="{{ route('leads.form') }}" target="_blank">
                    {{route('leads.form')}}
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

