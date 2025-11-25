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
        <div class="p-4 document mce-content-body">
            {!! $page->content !!}
        </div>
    </div>
</div>
@endsection
