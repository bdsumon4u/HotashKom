@push('css')
<!-- dropzone and datatables css are already loaded globally -->
@endpush

@push('js')
<script src="{{ asset('assets/js/dropzone/dropzone.js') }}" defer></script>
<script src="{{ asset('assets/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>
@endpush

<!-- The Modal -->
<div class="modal" id="multi-picker">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header p-3">
                <h4 class="modal-title">Image Picker</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body p-3">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="card rounded-0">
                            <div class="card-body">
                                <x-form method="post" :action="route('admin.images.store')" id="image-dropzone-multi" class="dropzone" has-files>
                                    <div class="dz-message needsclick">
                                        <i class="icon-cloud-up"></i>
                                        <h6>Drop files here or click to upload.</h6>
                                        <span class="note needsclick">(Recommended <strong>700x700</strong> dimension.)</span>
                                    </div>
                                </x-form>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover multi-picker w-100" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th width="150">Preview</th>
                                        <th>Filename</th>
                                        {{-- <th>Mime</th>
                                        <th>Size</th> --}}
                                        <th width="10">Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal footer -->
            <div class="modal-footer p-3">
                <button type="button" class="btn btn-done btn-success" style="display: none;"></button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let tableMulti = null;
    let dropzoneMulti = null;

    function initMultiPicker() {
        runWhenJQueryReady(function($) {
            // Ensure DataTables is loaded before initialization
            function initDataTable() {
                if (typeof $.fn.DataTable === 'undefined') {
                    // Wait a bit and try again if DataTables isn't loaded yet
                    setTimeout(initDataTable, 50);
                    return;
                }

                if ($.fn.dataTable && $.fn.dataTable.isDataTable && $.fn.dataTable.isDataTable('.multi-picker')) {
                    $('.multi-picker').DataTable().destroy();
                }

                tableMulti = $('.multi-picker').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{!! route('api.images.multiple') !!}",
                    columns: [
                        { data: 'id' },
                        { data: 'preview' },
                        { data: 'filename', name: 'filename' },
                        { data: 'action' },
                    ],
                    order: [[0, 'desc']],
                    lengthMenu: [[10, 25, 50, 100, 250, 500], [10, 25, 50, 100, 250, 500]],
                });

                var selected = @json($selected ?? []);

                $('#multi-picker').on('click', '.select-image', function (ev) {
                    if (selected.includes($(this).data('id'))) {
                        return $.notify('<i class="fa fa-bell-o mr-1"></i> Additional image already selected', {
                            type: 'success',
                            allow_dismiss: true,
                            showProgressbar: true,
                            timer: 300,
                            z_index: 9999,
                            animate:{ enter:'animated fadeInDown', exit:'animated fadeOutUp' }
                        });
                    }
                    selected.push($(this).data('id'));
                    $('.additional_images-previews').append('<div id="preview-'+$(this).data('id')+`" class="additional_images-preview position-relative" style="height: 150px; width: 150px; margin: 5px;">
                        <i class="fa fa-times text-danger position-absolute" style="font-size: large; top: 0; right: 0; background: #ddd; padding: 2px; border-radius: 3px; cursor: pointer;" onclick="this.parentNode.remove()"></i>
                        <img src="`+$(this).data('src')+`" alt="Additional Image" data-toggle="modal" data-target="#multi-picker" id="additional_image-preview" class="img-thumbnail img-responsive">
                        <input type="hidden" name="additional_images[]" value="`+$(this).data('id')+`">
                        <input type="hidden" name="additional_images_srcs[]" value="`+$(this).data('src')+`">
                    </div>`);
                    $.notify('<i class="fa fa-bell-o mr-1"></i> Additional image selected', {
                        type: 'success',
                        allow_dismiss: true,
                        showProgressbar: true,
                        timer: 300,
                        z_index: 9999,
                        animate:{ enter:'animated fadeInDown', exit:'animated fadeOutUp' }
                    });
                });

                // Initialize Dropzone when modal is shown
                $('#multi-picker').on('shown.bs.modal', function () {
                    // Wait for Dropzone to be available (since it's loaded with defer)
                    function initDropzone() {
                        if (window.Dropzone) {
                            // Destroy existing instance if any
                            if (dropzoneMulti) {
                                try {
                                    dropzoneMulti.destroy();
                                } catch(e) {}
                                dropzoneMulti = null;
                            }

                            // Clean up any other instances on this element
                            const element = document.getElementById('image-dropzone-multi');
                            if (element && element.dropzone) {
                                try {
                                    element.dropzone.destroy();
                                } catch(e) {}
                            }

                            Dropzone.autoDiscover = false;
                            dropzoneMulti = new Dropzone("#image-dropzone-multi", {
                                clickable: true,
                                init: function () {
                                    this.on('complete', function(){
                                        if(this.getQueuedFiles().length === 0 && this.getUploadingFiles().length === 0) {
                                            if (tableMulti) {
                                                tableMulti.ajax.reload(null, false);
                                            }
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
                });
            }

            // Start initialization
            initDataTable();
        });
    }

    document.addEventListener('DOMContentLoaded', initMultiPicker, { once: true });
    if (document.readyState !== 'loading') {
        initMultiPicker();
    }
</script>
@endpush
