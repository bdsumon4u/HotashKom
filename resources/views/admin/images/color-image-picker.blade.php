<!-- The Modal -->
<div class="modal" id="color-image-picker-{{ $colorOptionId }}">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="p-3 modal-header">
                <h4 class="modal-title">Select Image for {{ $colorOptionName }}</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <!-- Modal body -->
            <div class="p-3 modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="card rounded-0">
                            <div class="card-body">
                                <x-form method="post" :action="route('admin.images.store', ['resize' => '700x700'])" id="image-dropzone-color-{{ $colorOptionId }}" class="dropzone" has-files>
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
                            <table class="table table-bordered table-striped table-hover color-image-picker-{{ $colorOptionId }} w-100" style="width: 100%;">
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

@push('scripts')
<script>
    var tableColorImage_{{ $colorOptionId }} = $('.color-image-picker-{{ $colorOptionId }}').DataTable({
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

    tableColorImage_{{ $colorOptionId }}.on('draw', function () {
        $('#color-image-select-{{ $selected ?? 0 }}').prop('checked', true);
    });

    $('#color-image-picker-{{ $colorOptionId }}').on('click', '.select-image', function (ev) {
        const selectedImageId = $(this).data('id');
        const selectedImageSrc = $(this).data('src');

        // Update the preview image and hidden input for this color
        const previewContainer = $('#color-preview-{{ $colorOptionId }}');
        previewContainer.find('.no-image-text').remove();
        previewContainer.find('img').remove();
        previewContainer.append(`<img src="${selectedImageSrc}" alt="{{ $colorOptionName }}" data-toggle="modal" data-target="#color-image-picker-{{ $colorOptionId }}" class="img-thumbnail" style="max-width: 100px; cursor: pointer;">`);

        $('.color-image-input-{{ $colorOptionId }}').val(selectedImageId);

        $(this).parents('.modal').modal('hide');
        $.notify('<i class="mr-1 fa fa-bell-o"></i> Image selected for {{ $colorOptionName }}', {
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

    Dropzone.options.imageDropzoneColor{{ $colorOptionId }} = {
        init: function () {
            this.on('complete', function(){
                if(this.getQueuedFiles().length == 0 && this.getUploadingFiles().length == 0) {
                    tableColorImage_{{ $colorOptionId }}.ajax.reload();
                }
            });
        }
    };
</script>
@endpush

