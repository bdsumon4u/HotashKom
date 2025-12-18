@extends('layouts.light.master')
@section('title', 'Images')

@push('css')
<link rel="stylesheet" type="text/css" href="{{asset('assets/css/dropzone.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('assets/css/datatables.css')}}">
@endpush

@section('breadcrumb-title')
<h3>Images</h3>
@endsection

@section('breadcrumb-items')
<li class="breadcrumb-item">Images</li>
@endsection

@push('js')
<script src="{{ asset('assets/js/dropzone/dropzone.js') }}" defer></script>
<script src="{{ asset('assets/js/datatable/datatables/jquery.dataTables.min.js') }}" defer></script>
@endpush

@push('styles')
<style>
    form#drop-imgs {
        margin-bottom: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    th,
    td {
        vertical-align: middle !important;
    }
    table.dataTable tbody td.select-checkbox:before,
    table.dataTable tbody td.select-checkbox:after,
    table.dataTable tbody th.select-checkbox:before,
    table.dataTable tbody th.select-checkbox:after {
        top: 50%;
    }
</style>
@endpush

@section('content')
<div class="mb-5 row">
    <div class="col-sm-12">
        <div class="card rounded-0">
            <div class="card-body">
                <x-form method="post" :action="route('admin.images.store')" id="image-dropzone" class="dropzone" has-files>
                    <div class="dz-message needsclick">
                        <i class="icon-cloud-up"></i>
                        <h6>Drop files here or click to upload.</h6>
                        <span class="note needsclick">(Recommended <strong>700x700</strong> dimension.)</span>
                    </div>
                </x-form>
            </div>
        </div>
        <div class="card rounded-0">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover datatable" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th width="100">Preview</th>
                                <th>Filename</th>
                                <th>Mime</th>
                                <th>Size</th>
                                <th width="10">Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function initImagesIndex() {
        // Destroy existing table if any (SPA safety)
        if ($.fn.dataTable && $.fn.dataTable.isDataTable && $.fn.dataTable.isDataTable('.datatable')) {
            $('.datatable').DataTable().destroy();
        }

        const table = $('.datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{!! route('api.images.index') !!}",
            columns: [
                { data: 'id' },
                { data: 'preview' },
                { data: 'filename', name: 'filename' },
                { data: 'mime', name: 'mime' },
                { data: 'size_human', name: 'size' },
                { data: 'action' },
            ],
            order: [[0, 'desc']],
            pageLength: 50,
            lengthMenu: [[10, 25, 50, 100, 250, 500], [10, 25, 50, 100, 250, 500]],
        });

        // Wait for Dropzone to be available (since it's loaded with defer)
        function initDropzone() {
            if (window.Dropzone) {
                // Clean up existing Dropzones
                if (Array.isArray(Dropzone.instances)) {
                    Dropzone.instances.forEach(function (dz) { try { dz.destroy(); } catch(e) {} });
                    Dropzone.instances = [];
                }
                Dropzone.autoDiscover = false;
                // Manual init with explicit clickable target
                new Dropzone("#image-dropzone", {
                    // Make the entire dropzone clickable
                    clickable: true,
                    init: function () {
                        this.on('complete', function(){
                            if(this.getQueuedFiles().length === 0 && this.getUploadingFiles().length === 0) {
                                table.ajax.reload(null, false);
                            }
                        });
                    }
                });
            } else {
                // Retry after a short delay if Dropzone isn't loaded yet
                setTimeout(initDropzone, 100);
            }
        }
        initDropzone();
    }

    document.addEventListener('DOMContentLoaded', initImagesIndex, { once: true });
    if (document.readyState !== 'loading') {
        initImagesIndex();
    }
</script>
@endpush
