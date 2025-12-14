@extends('layouts.errors.master')
@section('title', 'Server Error')

@section('css')
@endsection

@section('style')
<style>
    .error-page {
        padding: 4rem 2rem;
        text-align: center;
    }
    .error-page__title {
        font-size: 8rem;
        font-weight: 700;
        line-height: 1;
        color: #dc3545;
        margin-bottom: 1rem;
    }
    .error-page__subtitle {
        font-size: 2rem;
        font-weight: 600;
        margin-bottom: 1rem;
        color: #333;
    }
    .error-page__text {
        font-size: 1.125rem;
        color: #666;
        margin-bottom: 2rem;
        line-height: 1.6;
    }
    .error-page__actions {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        align-items: center;
    }
    @media (min-width: 576px) {
        .error-page__actions {
            flex-direction: row;
            justify-content: center;
        }
    }
    .btn-custom {
        min-width: 200px;
        padding: 0.75rem 2rem;
        font-size: 1rem;
        border-radius: 0.375rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
    }
    .btn-primary-custom {
        background-color: #007bff;
        color: white;
        border: none;
    }
    .btn-primary-custom:hover {
        background-color: #0056b3;
        color: white;
        text-decoration: none;
    }
    .btn-secondary-custom {
        background-color: #6c757d;
        color: white;
        border: none;
    }
    .btn-secondary-custom:hover {
        background-color: #545b62;
        color: white;
        text-decoration: none;
    }
</style>
@endsection

@section('content')
<div class="page-wrapper compact-wrapper" id="pageWrapper">
    <div class="error-wrapper" style="min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 2rem;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 col-md-10 text-center">
                    <div class="error-page">
                        <div class="error-page__content mb-4">
                            <h1 class="error-page__title">500</h1>
                            <h2 class="error-page__subtitle">Internal Server Error</h2>
                            <p class="error-page__text">
                                We're sorry, but something went wrong on our end. Our team has been notified and is working to fix the issue. Please try again later.
                            </p>
                        </div>
                        <div class="error-page__actions">
                            <a href="{{ url('/') }}" class="btn-custom btn-primary-custom">
                                <i class="fas fa-home"></i> Go to Homepage
                            </a>
                            <button onclick="window.history.back()" class="btn-custom btn-secondary-custom">
                                <i class="fas fa-arrow-left"></i> Go Back
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
@endsection
