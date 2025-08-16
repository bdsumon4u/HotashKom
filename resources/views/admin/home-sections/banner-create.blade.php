@extends('layouts.light.master')
@section('title', 'Create Banner Section')

@include('admin.home-sections.partials.banner-styles')

@section('breadcrumb-title')
<h3>Create Banner Section</h3>
@endsection

@section('breadcrumb-items')
<li class="breadcrumb-item">Create Banner Section</li>
@endsection

@section('content')
<div class="mb-3 row justify-content-center">
    <div class="col-md-10">
        <x-form :action="route('admin.home-sections.store', ['banner' => true])" method="POST" class="shadow-sm card rounded-0">
            <div class="p-2 card-header d-flex justify-content-between align-items-center">
                <div><strong>Banner Section</strong></div>
                <button type="submit" class="btn btn-primary btn-sm">Save Section</button>
            </div>
            <div class="p-2 card-body">
                <livewire:banner-section :$categories />
            </div>
        </x-form>
    </div>
</div>

@include('admin.home-sections.partials.banner-preview')

@include('admin.images.single-picker', ['selected' => old('base_image', 0), 'resize' => false])
@endsection

@include('admin.home-sections.partials.banner-scripts')

@push('scripts')
<!-- Auto-save functionality for create page -->
<script>
    // Auto-save draft functionality (optional)
    let autoSaveTimer;
    function startAutoSave() {
        clearTimeout(autoSaveTimer);
        autoSaveTimer = setTimeout(() => {
            // Save draft to localStorage
            const formData = new FormData($('form')[0]);
            const draftData = {};
            for (let [key, value] of formData.entries()) {
                draftData[key] = value;
            }
            localStorage.setItem('banner_section_draft', JSON.stringify(draftData));

            // Show auto-save indicator
            $('.card-header').append('<small class="ml-2 text-muted" id="auto-save-indicator"><i class="mr-1 fa fa-save"></i>Draft saved</small>');
            setTimeout(() => $('#auto-save-indicator').fadeOut(), 2000);
        }, 30000); // Save every 30 seconds
    }

    // Load draft on page load
    $(document).ready(function() {
        const draft = localStorage.getItem('banner_section_draft');
        if (draft && confirm('A draft was found. Would you like to restore it?')) {
            // Load draft logic here
            console.log('Draft found:', JSON.parse(draft));
        }

        // Start auto-save
        $('input, select, textarea').on('input change', startAutoSave);
    });
</script>
@endpush
