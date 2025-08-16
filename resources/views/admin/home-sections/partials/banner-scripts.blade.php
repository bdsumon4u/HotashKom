@push('js')
<script src="{{asset('assets/js/select2/select2.full.min.js')}}"></script>
<script src="{{asset('assets/js/select2/select2-custom.js')}}"></script>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/gh/livewire/sortable@v1.x.x/dist/livewire-sortable.js"></script>
<script>
    $(document).ready(function(){
        // Initialize Select2 for category dropdowns
        $('select[multiple]').select2({
            placeholder: 'Select categories...',
            allowClear: true,
            width: '100%'
        });
    });
</script>
<script>
    $(document).ready(function() {
        $('input[type="file"]').change(function() {
            var $img = $(this).parent().find('img');
            if (this.files && this.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $img.attr('src', e.target.result);
                };
                reader.readAsDataURL(this.files[0]);
            }
        });

        // Auto-update preview when width changes
        $('input[name^="data[columns][width]"]').on('input', function() {
            updatePreview();
        });

        // Initialize tooltips with proper positioning
        $('[title]').tooltip({
            placement: 'auto',
            container: 'body',
            trigger: 'hover'
        });

        // Add form validation before submission
        $('form').on('submit', function(e) {
            if ($('.banner-builder').length > 0) {
                if (!validateBannerImages()) {
                    e.preventDefault();
                    return false;
                }
            }
        });

        // Enable keyboard shortcuts
        $(document).keydown(function(e) {
            if (e.ctrlKey || e.metaKey) {
                switch(String.fromCharCode(e.which).toLowerCase()) {
                    case 's':
                        e.preventDefault();
                        if ($('.banner-builder').length > 0) {
                            if (validateBannerImages()) {
                                $('form').submit();
                            }
                        } else {
                            $('form').submit();
                        }
                        break;
                    case 'n':
                        e.preventDefault();
                        window.Livewire.find(document.querySelector('[wire\\:id]').getAttribute('wire:id')).call('addColumn');
                        break;
                }
            }
        });
    });

    // Animation preview function
    function previewAnimations() {
        $('.banner-preview-item').each(function(index) {
            const $item = $(this);
            const animation = $item.data('animation');

            // Remove existing animation classes
            $item.removeClass(function (index, className) {
                return (className.match(/(^|\s)animate-\S+/g) || []).join(' ');
            });

            // Add animation class with delay
            setTimeout(() => {
                $item.addClass('animate-' + animation.replace('-', '-'));
            }, index * 200);
        });

        // Show notification
        $.notify({
            icon: 'fa fa-magic',
            message: 'Animation preview started!'
        }, {
            type: 'info',
            delay: 2000,
            allow_dismiss: true,
            z_index: 9999,
            animate: {
                enter: 'animated fadeInDown',
                exit: 'animated fadeOutUp'
            }
        });
    }

    // Update preview function
    function updatePreview() {
        setTimeout(() => {
            $('.banner-preview-item').each(function() {
                const width = $(this).attr('class').match(/col-(\d+)/)?.[1] || 12;
                $(this).find('.badge').text('Col ' + width);
            });
        }, 100);
    }

            // Function to reinitialize Select2
    function reinitializeSelect2() {
        setTimeout(function() {
            $('select[multiple]').each(function() {
                const $select = $(this);
                let selectedValues = [];

                // Preserve selected values if Select2 was already initialized
                if ($select.hasClass('select2-hidden-accessible')) {
                    selectedValues = $select.val() || [];
                    $select.select2('destroy');
                }

                // Reinitialize with preserved values
                $select.select2({
                    placeholder: 'Select categories...',
                    allowClear: true,
                    width: '100%'
                });

                // Restore selected values
                if (selectedValues.length > 0) {
                    $select.val(selectedValues).trigger('change');
                }
            });
        }, 100);
    }

    // Function to reinitialize tooltips
    function reinitializeTooltips() {
        setTimeout(function() {
            $('[title]').tooltip('dispose').tooltip({
                placement: 'auto',
                container: 'body',
                trigger: 'hover'
            });
        }, 100);
    }

    // Function to reinitialize both tooltips and Select2
    function reinitializeUIComponents() {
        reinitializeTooltips();
        reinitializeSelect2();
    }

    // Function to validate banner images before form submission
    function validateBannerImages() {
        const livewireComponent = window.Livewire.find(document.querySelector('[wire\\:id]').getAttribute('wire:id'));

        if (!livewireComponent || !livewireComponent.columns || livewireComponent.columns.length === 0) {
            showValidationError('Please add at least one banner column.');
            return false;
        }

        const columnsWithoutImages = [];
        livewireComponent.columns.forEach((column, index) => {
            if (!column.image) {
                columnsWithoutImages.push(index + 1);
            }
        });

        if (columnsWithoutImages.length > 0) {
            const columnText = columnsWithoutImages.length === 1 ? 'column' : 'columns';
            const columnNumbers = columnsWithoutImages.join(', ');
            showValidationError(`Please select images for banner ${columnText}: ${columnNumbers}`);
            return false;
        }

        return true;
    }

    // Function to show validation error
    function showValidationError(message) {
        // Highlight columns without images
        highlightMissingImages();

        $.notify({
            icon: 'fa fa-exclamation-triangle',
            message: message
        }, {
            type: 'danger',
            delay: 5000,
            allow_dismiss: true,
            z_index: 9999,
            animate: {
                enter: 'animated shake fadeInDown',
                exit: 'animated fadeOutUp'
            }
        });
    }

    // Function to highlight columns without images
    function highlightMissingImages() {
        const livewireComponent = window.Livewire.find(document.querySelector('[wire\\:id]').getAttribute('wire:id'));

        if (livewireComponent && livewireComponent.columns) {
            $('.banner-column-card').each(function(index) {
                const $card = $(this);
                const column = livewireComponent.columns[index];

                if (!column || !column.image) {
                    $card.addClass('border-danger')
                         .find('.image-upload-placeholder')
                         .addClass('border-danger bg-danger text-white')
                         .find('.text-muted')
                         .removeClass('text-muted')
                         .addClass('text-white');

                    // Remove highlight after 3 seconds
                    setTimeout(() => {
                        $card.removeClass('border-danger')
                             .find('.image-upload-placeholder')
                             .removeClass('border-danger bg-danger text-white')
                             .find('.text-white:not(.text-muted)')
                             .removeClass('text-white')
                             .addClass('text-muted');
                    }, 3000);
                }
            });
        }
    }

        // Enhanced image picker integration
    let currentImageTarget = null;

    // Track which image placeholder was clicked
    $(document).on('click', '[data-toggle="modal"][data-target="#single-picker"]', function() {
        // Find the closest banner column card to identify which column we're working with
        const columnCard = $(this).closest('.banner-column-card');
        currentImageTarget = columnCard.find('.base_image-preview');

        // Store the column index for later use
        const columnIndex = $('.banner-column-card').index(columnCard);
        $(this).attr('data-column-index', columnIndex);
    });



    // Update preview section
    function updateExternalPreview() {
        const livewireComponent = window.Livewire.find(document.querySelector('[wire\\:id]').getAttribute('wire:id'));
        if (livewireComponent && livewireComponent.columns && livewireComponent.columns.length > 0) {
            const columns = livewireComponent.columns;
            let previewHtml = '';

            columns.forEach((column, i) => {
                const width = column.width || 12;
                const animation = column.animation || 'fade-right';
                const image = column.image;

                previewHtml += `<div class="col-${width} banner-preview-item p-1" data-animation="${animation}">`;

                if (image) {
                    // Handle different image path formats
                    let imagePath = image;
                    if (!imagePath.startsWith('http') && !imagePath.startsWith('/')) {
                        // If it's a relative path, make it absolute
                        imagePath = '/' + imagePath;
                    }

                                        previewHtml += `
                        <div class="position-relative">
                            <img src="${imagePath}" alt="Banner ${i+1}" class="rounded img-fluid w-100" />
                            <div class="preview-overlay">
                                <small class="badge badge-dark badge-sm">${width}</small>
                            </div>
                        </div>
                    `;
                } else {
                    previewHtml += `
                        <div class="text-white rounded banner-placeholder d-flex align-items-center justify-content-center bg-secondary">
                            <div class="text-center">
                                <i class="mb-1 fa fa-image"></i>
                                <br><small>Col ${i+1}</small>
                                <br><small class="badge badge-light badge-sm">${width}</small>
                            </div>
                        </div>
                    `;
                }

                previewHtml += '</div>';
            });

            $('#banner-preview-content').html(previewHtml);
            $('#banner-preview-section').show();
        } else {
            $('#banner-preview-section').hide();
        }
    }

        // Initialize preview functionality
    $(document).ready(function() {
                        // Listen for Livewire updates to refresh preview
        window.addEventListener('livewire:update', function() {
            updateExternalPreview();
            reinitializeUIComponents();
        });

                // Also listen for more specific Livewire events
        document.addEventListener('livewire:navigated', function() {
            updateExternalPreview();
        });

        // Listen for custom image-updated event
        window.addEventListener('image-updated', function(event) {
            setTimeout(updateExternalPreview, 100);
            setTimeout(reinitializeUIComponents, 150);
        });

        // Listen for custom image-removed event
        window.addEventListener('image-removed', function(event) {
            setTimeout(updateExternalPreview, 100);
            setTimeout(reinitializeUIComponents, 150);
        });

        // Listen for column-added event
        window.addEventListener('column-added', function(event) {
            setTimeout(updateExternalPreview, 100);
            setTimeout(reinitializeUIComponents, 150);
        });

        // Listen for column-removed event
        window.addEventListener('column-removed', function(event) {
            setTimeout(updateExternalPreview, 100);
            setTimeout(reinitializeUIComponents, 150);
        });

        // Listen for input changes to update preview
        $('body').on('input change', 'input[name^="data[columns]"], select[name^="data[columns]"]', function() {
            setTimeout(updateExternalPreview, 100);
        });

        // Initial preview update and UI component initialization
        setTimeout(updateExternalPreview, 500);
        setTimeout(reinitializeUIComponents, 600);

        // Ensure our image picker handler is set up after everything else
        setTimeout(function() {
            if ($('.banner-builder').length > 0) {
                                // Re-initialize the image picker handler for banner sections
                $('#single-picker').off('click', '.select-image').on('click', '.select-image', function (ev) {
                    ev.preventDefault();
                    ev.stopImmediatePropagation();

                    const imageSrc = $(this).data('src');
                    const imageId = $(this).data('id');

                    // Get the column index from the last clicked button
                    let columnIndex = -1;
                    const lastClickedButton = $('[data-toggle="modal"][data-target="#single-picker"][data-column-index]').last();
                    if (lastClickedButton.length > 0) {
                        columnIndex = parseInt(lastClickedButton.attr('data-column-index'));
                    }

                    // Update the Livewire component first - this will trigger a re-render
                    const livewireComponent = window.Livewire.find(document.querySelector('[wire\\:id]').getAttribute('wire:id'));
                    if (livewireComponent && livewireComponent.columns && columnIndex >= 0) {
                        // Call the Livewire method to update the image
                        livewireComponent.call('updateImage', columnIndex, imageSrc).then(() => {
                            // After Livewire updates, update external preview and reinitialize UI components
                            setTimeout(updateExternalPreview, 100);
                            setTimeout(reinitializeUIComponents, 200);
                        });
                    }

                    $(this).parents('.modal').modal('hide');

                    $.notify({
                        icon: 'fa fa-check',
                        message: 'Image selected successfully!'
                    }, {
                        type: 'success',
                        delay: 2000,
                        allow_dismiss: true,
                        z_index: 9999,
                        animate: {
                            enter: 'animated fadeInDown',
                            exit: 'animated fadeOutUp'
                        }
                    });

                    // Clean up the data attribute
                    $('[data-column-index]').removeAttr('data-column-index');

                    // Reset the target
                    currentImageTarget = null;

                    return false;
                });
            }
        }, 1000);
    });
</script>
@endpush
