@push('css')
<link rel="stylesheet" type="text/css" href="{{asset('assets/css/dropzone.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('assets/css/datatables.css')}}">
@endpush


<!-- The Modal -->
<div class="modal" id="variant-single-picker-{{ $variationId }}">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="p-3 modal-header">
                <h4 class="modal-title">Variant Image Picker</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="p-3 modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="card rounded-0">
                            <div class="card-body">
                                <x-form method="post" :action="route('admin.images.store')" id="image-dropzone-variant-{{ $variationId }}" class="dropzone" has-files>
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
                            <table class="table table-bordered table-striped table-hover variant-single-picker-{{ $variationId }} w-100" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th width="150">Preview</th>
                                        <th>Filename</th>
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

@push('js')
<script src="{{asset('assets/js/dropzone/dropzone.js')}}" defer></script>
<script src="{{asset('assets/js/dropzone/dropzone-script.js')}}" defer></script>
<script src="{{asset('assets/js/datatable/datatables/jquery.dataTables.min.js')}}" defer></script>
@endpush

@push('scripts')
<script>
    var tableVariantSingle{{ $variationId }} = $('.variant-single-picker-{{ $variationId }}').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{!! route('api.images.single') !!}",
        columns: [
            { data: 'id' },
            { data: 'preview' },
            { data: 'filename', name: 'filename' },
            { data: 'action' },
        ],
        order: [
            [0, 'desc']
        ],
        lengthMenu: [[10, 25, 50, 100, 250, 500], [10, 25, 50, 100, 250, 500]],
    });

    tableVariantSingle{{ $variationId }}.on('draw', function () {
        $('#single-select-{{ $selected ?? 0 }}').prop('checked', true);
    });

    $('#variant-single-picker-{{ $variationId }}').on('click', '.select-image', function (ev) {
        var variationId = '{{ $variationId }}';
        var imageSrc = $(this).data('src');
        var imageId = $(this).data('id');
        
        // Update the preview image
        var previewHtml = '<img src="' + imageSrc + '" alt="Variant Image" data-toggle="modal" data-target="#variant-single-picker-' + variationId + '" class="img-thumbnail img-responsive" style="cursor: pointer; width: 100%; margin: 5px; margin-left: 0;">';
        previewHtml += '<input type="hidden" name="variations[{{ $variationIndex }}][base_image_id]" value="' + imageId + '" class="variant-base-image-id">';
        
        $('#variant-preview-' + variationId).html(previewHtml).removeClass('d-none');
        
        $(this).parents('.modal').modal('hide');
        $.notify('<i class="mr-1 fa fa-bell-o"></i> Variant image selected', {
            type: 'success',
            allow_dismiss: true,
            showProgressbar: true,
            timer: 300,
            z_index: 9999,
            animate:{
                enter:'animated fadeInDown',
                exit:'animated fadeOutUp'
            }
        });
    });

    Dropzone.options['imageDropzoneVariant{{ $variationId }}'] = {
        init: function () {
            this.on('complete', function(){
                if(this.getQueuedFiles().length == 0 && this.getUploadingFiles().length == 0) {
                    tableVariantSingle{{ $variationId }}.ajax.reload();
                }
            });
        }
    };
</script>
@endpush

