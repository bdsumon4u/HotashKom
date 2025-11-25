@extends('layouts.yellow.master')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="border-0 shadow-sm card">
                <div class="p-4 card-body p-md-5">
                    <h1 class="mb-1 h4">Register Your Interest</h1>
                    <p class="mb-4 text-muted">Share your details and we will reach out with the next steps.</p>

                    @if (session('lead_submitted'))
                        <div class="alert alert-success">
                            {{ session('lead_message') }}
                        </div>
                    @endif

                    <form action="{{ route('leads.store') }}" method="POST" novalidate>
                        @csrf
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="lead-name" class="form-label">Name <span class="text-danger">*</span></label>
                                <input
                                    type="text"
                                    id="lead-name"
                                    name="name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name') }}"
                                    placeholder="Full name"
                                    required
                                >
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="lead-shop-name" class="form-label">Shop Name</label>
                                <input
                                    type="text"
                                    id="lead-shop-name"
                                    name="shop_name"
                                    class="form-control @error('shop_name') is-invalid @enderror"
                                    value="{{ old('shop_name') }}"
                                    placeholder="Business name (optional)"
                                >
                                @error('shop_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="lead-email" class="form-label">Email</label>
                                <input
                                    type="email"
                                    id="lead-email"
                                    name="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email') }}"
                                    placeholder="Email address (optional)"
                                >
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="lead-phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                                <input
                                    type="text"
                                    id="lead-phone"
                                    name="phone"
                                    class="form-control @error('phone') is-invalid @enderror"
                                    value="{{ old('phone') }}"
                                    placeholder="01XXXXXXXXX"
                                    required
                                >
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 form-group">
                                <label for="lead-message" class="form-label">Message To Us</label>
                                <textarea
                                    id="lead-message"
                                    name="message"
                                    rows="5"
                                    class="form-control @error('message') is-invalid @enderror"
                                    placeholder="Tell us about your business, requirements or questions"
                                >{{ old('message') }}</textarea>
                                @error('message')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="gap-2 mt-4 d-grid d-md-flex">
                            <button type="submit" class="px-4 btn btn-primary">Submit</button>
                            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">Back</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

