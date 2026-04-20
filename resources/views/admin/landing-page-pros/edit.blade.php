@extends('layouts.light.master')
@section('title', 'Edit Landing Page Pro')

@section('breadcrumb-title')
    <h3>Edit Landing Page Pro</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">Landing Page Pro</li>
    <li class="breadcrumb-item">Edit</li>
@endsection

@section('content')
    <div class="row mb-5">
        <div class="col-sm-12">
            <div class="card rounded-0 shadow-sm">
                <div class="p-3 card-header">Edit <strong>Landing Page Pro</strong></div>
                <div class="p-3 card-body">
                    <x-form action="{{ route('admin.landing-page-pros.update', $landingPagePro) }}" method="patch">
                        @include('admin.landing-page-pros.form')
                    </x-form>

                    @include('admin.landing-page-pros.image-picker-modal')
                </div>
            </div>
        </div>
    </div>
@endsection
