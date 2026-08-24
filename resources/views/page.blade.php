@extends('layouts.yellow.master')

@section('seo_tags')
    <title>{{ $page->seo_title ?: $page->title . ' | ' . ($company->name ?? config('app.name')) }}</title>
    <meta
        name="description"
        content="{{ $page->meta_description ?: \Illuminate\Support\Str::limit(trim(preg_replace('/\s+/', ' ', strip_tags($page->content))), 160, '') }}"
    >
@endSection

@section('title', $page->title)

@section('content')

@if ($page->slug === 'about-us')
    @include('pages.about-us-premium', ['page' => $page])
@elseif ($page->slug === 'contact-us')
    @if(request()->path() === 'terms-and-conditions')
    {{-- CLOUDFLARE_EMAIL_OFF_TERMS --}}
    <!--email_off-->
    {!! $page->content !!}
    <!--/email_off-->
    @else
        {!! $page->content !!}
    @endif
@else
    @include('partials.page-header', [
        'paths' => [
            url('/') => 'Home',
        ],
        'active' => $page->title,
        'page_title' => $page->title
    ])

    <div class="block">
        <div class="container">
            <div class="p-4 document mce-content-body">
                @if(request()->path() === 'terms-and-conditions')
                    {{-- CLOUDFLARE_EMAIL_OFF_TERMS --}}
                    <!--email_off-->
                    {!! $page->content !!}
                    <!--/email_off-->
                @else
                    {!! $page->content !!}
                @endif
            </div>
        </div>
    </div>
@endif

@endsection
