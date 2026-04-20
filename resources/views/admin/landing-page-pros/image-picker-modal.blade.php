<div class="modal" id="landing-pro-image-picker" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="p-3 modal-header">
                <h5 class="modal-title">Image Picker</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="p-3 modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="card rounded-0">
                            <div class="card-body">
                                <x-form method="post" :action="route('admin.images.store')" id="landing-pro-dropzone" class="dropzone"
                                    has-files>
                                    <div class="dz-message needsclick">
                                        <i class="icon-cloud-up"></i>
                                        <h6>Drop files here or click to upload.</h6>
                                        <span class="note needsclick">(Recommended <strong>700x700</strong>
                                            dimension.)</span>
                                    </div>
                                </x-form>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-12">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover w-100"
                                id="landing-pro-image-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th width="150">Preview</th>
                                        <th>Filename</th>
                                        <th width="20">Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-3 modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        window.landingProImagePicker = window.landingProImagePicker || {
            table: null,
            dropzone: null,
            onSelect: null,
            closeOnSelect: true,
            bindingsInitialized: false,

            init() {
                runWhenJQueryReady(($) => {
                    this.initTable();
                    this.initDropzone();
                    this.bindSelection();
                });
            },

            initTable() {
                if (this.table) {
                    return;
                }

                if (typeof $.fn.DataTable === 'undefined') {
                    setTimeout(() => this.initTable(), 75);
                    return;
                }

                this.table = $('#landing-pro-image-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{!! route('api.images.multiple') !!}",
                    columns: [{
                            data: 'id'
                        },
                        {
                            data: 'preview'
                        },
                        {
                            data: 'filename',
                            name: 'filename'
                        },
                        {
                            data: 'action'
                        },
                    ],
                    order: [
                        [0, 'desc']
                    ],
                    lengthMenu: [
                        [10, 25, 50, 100],
                        [10, 25, 50, 100]
                    ],
                });
            },

            initDropzone() {
                if (!window.Dropzone) {
                    setTimeout(() => this.initDropzone(), 150);
                    return;
                }

                if (this.dropzone) {
                    return;
                }

                Dropzone.autoDiscover = false;
                this.dropzone = new Dropzone('#landing-pro-dropzone', {
                    clickable: true,
                    init: function() {
                        this.on('complete', () => {
                            if (this.getQueuedFiles().length === 0 && this
                                .getUploadingFiles().length === 0) {
                                window.landingProImagePicker.table?.ajax.reload(null, false);
                            }
                        });
                    },
                });
            },

            bindSelection() {
                if (this.bindingsInitialized) {
                    return;
                }

                $('#landing-pro-image-picker').off('click.landingProPicker').on('click.landingProPicker',
                    '.select-image', (event) => {
                        const $target = $(event.currentTarget);
                        const payload = {
                            id: Number($target.data('id')),
                            src: String($target.data('src')),
                        };

                        if (typeof this.onSelect === 'function') {
                            this.onSelect(payload);
                        }

                        if (this.closeOnSelect) {
                            $('#landing-pro-image-picker').modal('hide');
                        }
                    });

                this.bindingsInitialized = true;
            },

            open({
                onSelect,
                closeOnSelect = true,
            } = {}) {
                this.onSelect = onSelect ?? null;
                this.closeOnSelect = Boolean(closeOnSelect);

                if (!this.table) {
                    this.initTable();
                }

                if (!this.dropzone) {
                    this.initDropzone();
                }

                this.table?.ajax.reload(null, false);
                $('#landing-pro-image-picker').modal('show');
            },
        };

        document.addEventListener('DOMContentLoaded', () => {
            window.landingProImagePicker.init();
        });
    </script>
@endpush
