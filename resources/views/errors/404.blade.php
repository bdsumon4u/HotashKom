@extends('layouts.yellow.master')
@section('title', 'Page Not Found')

@section('content')
<div class="block">
    <div class="container">
        <div class="py-5 row justify-content-center">
            <div class="text-center col-lg-8 col-md-10">
                <div class="error-page">
                    <div class="mb-4 error-page__content">
                        <h1 class="error-page__title" style="font-size: 8rem; font-weight: 700; line-height: 1; color: #ff6b6b; margin-bottom: 1rem;">404</h1>
                        <h2 class="error-page__subtitle" style="font-size: 2rem; font-weight: 600; margin-bottom: 1rem; color: #333;">Page Not Found</h2>
                        <p class="error-page__text" style="font-size: 1.125rem; color: #666; margin-bottom: 2rem; line-height: 1.6;">
                            Oops! The page you're looking for doesn't exist. It might have been moved, deleted, or the URL might be incorrect.
                        </p>
                    </div>
                    <div class="gap-3 error-page__actions d-flex flex-column flex-sm-row justify-content-center">
                        <a href="{{ url('/') }}" class="btn btn-primary btn-lg" style="min-width: 200px;">
                            <i class="mr-2 fas fa-home"></i> Go to Homepage
                        </a>
                        <button onclick="window.history.back()" class="btn btn-secondary btn-lg" style="min-width: 200px;">
                            <i class="mr-2 fas fa-arrow-left"></i> Go Back
                        </button>
                    </div>
                    <div class="pt-4 mt-5 error-page__help" style="border-top: 1px solid #e5e5e5;">
                        <p class="mb-2 text-muted">Need help? Try these links:</p>
                        <div class="flex-wrap gap-3 d-flex justify-content-center">
                            <a href="{{ url('/shop') }}" class="text-decoration-none" style="color: #007bff;">Browse Products</a>
                            <span class="text-muted">•</span>
                            <a href="{{ url('/') }}" class="text-decoration-none" style="color: #007bff;">Home</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
