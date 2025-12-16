@push('css')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/dropzone.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/datatables.css') }}">
@endpush

@push('js')
<script src="{{ asset('assets/js/dropzone/dropzone.js') }}" defer></script>
<script src="{{ asset('assets/js/dropzone/dropzone-script.js') }}" defer></script>
<script src="{{ asset('assets/js/datatable/datatables/jquery.dataTables.min.js') }}" defer></script>
@endpush


<!-- The Modal -->
<div class="modal" id="single-picker">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="p-3 modal-header">
                <h4 class="modal-title">Image Picker</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="p-3 modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="card rounded-0">
                            <div class="card-body">
                                <x-form method="post" :action="route('admin.images.store', isset($resize) ? ['resize' => $resize] : [])" id="image-dropzone-single" class="dropzone" has-files>
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
                            <table class="table table-bordered table-striped table-hover single-picker w-100" style="width: 100%;">
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
            <div class="p-3 modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function initSinglePicker() {
        if ($.fn.dataTable && $.fn.dataTable.isDataTable && $.fn.dataTable.isDataTable('.single-picker')) {
            $('.single-picker').DataTable().destroy();
        }

        const tableSingle = $('.single-picker').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{!! route('api.images.single') !!}",
            columns: [
                { data: 'id' },
                { data: 'preview' },
                { data: 'filename', name: 'filename' },
                { data: 'action' },
            ],
            order: [[0, 'desc']],
            lengthMenu: [[10, 25, 50, 100, 250, 500], [10, 25, 50, 100, 250, 500]],
        });

        tableSingle.on('draw', function () {
            $('#single-select-{{ $selected ?? 0 }}').prop('checked', true);
        });

        $('#single-picker').on('click', '.select-image', function (ev) {
            $('.base_image-preview').html('<img src="'+$(this).data('src')+'" alt="Base Image" data-toggle="modal" data-target="#single-picker" id="base_image-preview" class="img-thumbnail img-responsive" style="width: 100%; margin: 5px; margin-left: 0;"><input type="hidden" name="base_image" value="'+$(this).data('id')+'"><input type="hidden" name="base_image_src" value="'+$(this).data('src')+'">').removeClass('d-none');
            $(this).parents('.modal').modal('hide');
            $.notify('<i class="mr-1 fa fa-bell-o"></i> Base image selected', {
                type: 'success',
                allow_dismiss: true,
                showProgressbar: true,
                timer: 300,
                z_index: 9999,
                animate:{ enter:'animated fadeInDown', exit:'animated fadeOutUp' }
            });
        });

        if (window.Dropzone) {
            if (Array.isArray(Dropzone.instances)) {
                Dropzone.instances.forEach(function (dz) { try { dz.destroy(); } catch(e) {} });
                Dropzone.instances = [];
            }
            Dropzone.autoDiscover = false;
            new Dropzone("#image-dropzone-single", {
                clickable: "#image-dropzone-single, #image-dropzone-single .dz-message",
                init: function () {
                    this.on('complete', function(){
                        if(this.getQueuedFiles().length === 0 && this.getUploadingFiles().length === 0) {
                            tableSingle.ajax.reload(null, false);
                        }
                    });
                }
            });
        }
    }

    document.addEventListener('DOMContentLoaded', initSinglePicker, { once: true });
    if (document.readyState !== 'loading') {
        initSinglePicker();
    }
</script>
@endpush
