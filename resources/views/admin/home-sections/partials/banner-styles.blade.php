@push('css')
<link rel="stylesheet" type="text/css" href="{{asset('assets/css/select2.css')}}">
@endpush

@push('styles')
<style>
    .select2 {
        width: 100% !important;
    }
    .select2-selection.select2-selection--multiple {
        display: flex;
        align-items: center;
    }
    .select2-container .select2-selection--single {
        border-color: #ced4da !important;
    }

    /* Banner Builder Styles - Compact Version */
    .banner-builder {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .banner-column-card {
        transition: all 0.2s ease;
        border-radius: 2px;
        overflow: hidden;
        margin-bottom: 0.75rem !important;
    }

    .banner-column-card:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.08) !important;
    }

    .cursor-move {
        cursor: move;
    }

    .image-preview-wrapper {
        position: relative;
        overflow: hidden;
        border-radius: 2px;
        transition: transform 0.2s ease;
    }

    .image-preview-wrapper:hover {
        transform: scale(1.01);
    }

    .image-overlay {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        opacity: 0;
        transition: opacity 0.2s ease;
        background: rgba(0,0,0,0.6);
        border-radius: 2px;
        padding: 4px;
    }

    .image-preview-wrapper:hover .image-overlay {
        opacity: 1;
    }

    .image-upload-placeholder {
        min-height: 100px;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        transition: all 0.2s ease;
        cursor: pointer;
        padding: 1rem !important;
    }

    .image-upload-placeholder:hover {
        background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
        border-color: #007bff !important;
    }

    .banner-preview-item {
        transition: all 0.2s ease;
    }

    .banner-preview-item:hover {
        transform: scale(1.01);
    }

    .preview-overlay {
        position: absolute;
        top: 4px;
        right: 4px;
        z-index: 10;
    }

    .banner-placeholder {
        min-height: 80px;
        background: linear-gradient(135deg, #6c757d 0%, #495057 100%) !important;
        border: 2px dashed #adb5bd;
        border-radius: 2px;
        transition: all 0.2s ease;
    }

    .banner-placeholder:hover {
        border-color: #007bff;
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%) !important;
    }

    .form-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 4px !important;
        font-size: 0.875rem;
    }

    /* Column header styling */
    .banner-builder .card-header h6 {
        color: #343a40 !important;
        font-weight: 600;
    }

    .banner-builder .card-header .text-primary {
        color: #007bff !important;
    }

    .bg-gradient-primary {
        background: linear-gradient(135deg, #007bff 0%, #6610f2 100%) !important;
    }

    /* Compact spacing overrides */
    .banner-builder .card-body {
        padding: 0.75rem !important;
    }

    .banner-builder .card-header {
        padding: 0.5rem 0.75rem !important;
    }

    .banner-builder .mb-3 {
        margin-bottom: 0.75rem !important;
    }

    .banner-builder .mb-4 {
        margin-bottom: 1rem !important;
    }

    .banner-builder .row {
        margin-left: -0.375rem;
        margin-right: -0.375rem;
    }

    .banner-builder .row > [class*="col-"] {
        padding-left: 0.375rem;
        padding-right: 0.375rem;
    }

    .banner-builder .form-text {
        font-size: 0.75rem;
        margin-top: 0.25rem;
    }

    .banner-builder .input-group-text {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }

    .banner-builder .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }

    /* Animation preview styles */
    .animate-preview {
        animation-duration: 1s;
    }

    @keyframes fadeInLeft {
        from { opacity: 0; transform: translateX(-30px); }
        to { opacity: 1; transform: translateX(0); }
    }

    @keyframes fadeInRight {
        from { opacity: 0; transform: translateX(30px); }
        to { opacity: 1; transform: translateX(0); }
    }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes fadeInDown {
        from { opacity: 0; transform: translateY(-30px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .animate-fade-left { animation: fadeInLeft 1s ease; }
    .animate-fade-right { animation: fadeInRight 1s ease; }
    .animate-fade-up { animation: fadeInUp 1s ease; }
    .animate-fade-down { animation: fadeInDown 1s ease; }

    /* Sortable styles */
    .sortable-ghost {
        opacity: 0.4;
    }

    .sortable-chosen {
        cursor: grabbing;
    }

    .dropdown-menu {
        border: none;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        border-radius: 2px;
    }

    .dropdown-item {
        padding: 8px 16px;
        transition: all 0.2s ease;
    }

    .dropdown-item:hover {
        background: linear-gradient(135deg, #007bff 0%, #6610f2 100%);
        color: white;
    }

    .dropdown-header {
        font-size: 0.75rem;
        font-weight: 600;
        color: #6c757d !important;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 8px 16px 4px 16px;
    }

    .dropdown-divider {
        margin: 4px 0;
    }

    /* Tooltip improvements */
    .tooltip {
        z-index: 9999;
    }

    .tooltip-inner {
        background-color: rgba(0, 0, 0, 0.9);
        color: white;
        padding: 4px 8px;
        border-radius: 3px;
        font-size: 12px;
        max-width: 200px;
    }

    .tooltip.bs-tooltip-top .arrow::before {
        border-top-color: rgba(0, 0, 0, 0.9);
    }

    .tooltip.bs-tooltip-bottom .arrow::before {
        border-bottom-color: rgba(0, 0, 0, 0.9);
    }

    .tooltip.bs-tooltip-left .arrow::before {
        border-left-color: rgba(0, 0, 0, 0.9);
    }

    .tooltip.bs-tooltip-right .arrow::before {
        border-right-color: rgba(0, 0, 0, 0.9);
    }

    /* Validation error highlighting */
    .banner-column-card.border-danger {
        border-color: #dc3545 !important;
        animation: shake 0.5s ease-in-out;
    }

    .image-upload-placeholder.border-danger {
        border-color: #dc3545 !important;
        animation: pulse 1s infinite;
    }

    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }

    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.8; }
        100% { opacity: 1; }
    }
</style>
@endpush
