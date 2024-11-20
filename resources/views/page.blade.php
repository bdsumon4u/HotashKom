@extends('layouts.yellow.master')

@section('title', $page->title)

@section('content')

@include('partials.page-header', [
    'paths' => [
        url('/') => 'Home',
    ],
    'active' => $page->title,
    'page_title' => $page->title
])

<div class="block">
    <div class="container">
        <div class="document mce-content-body p-4">
            {!! $page->content !!}
        </div>
    </div>
</div>
@endsection