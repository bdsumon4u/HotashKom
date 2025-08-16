<div class="banner-builder">
    <!-- Error Messages -->
    <div class="row">
        <div class="col-12">
            @foreach($errors->all() as $error)
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="mr-2 fa fa-exclamation-triangle"></i>{{ $error }}
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Builder Instructions -->
    @if(empty($columns))
    <div class="mb-2 row">
        <div class="col-12">
            <div class="py-2 border-0 alert alert-info">
                <div class="d-flex align-items-center">
                    <i class="mr-2 fa fa-info-circle text-info"></i>
                    <div>
                        <h6 class="mb-1">Banner Section Builder</h6>
                        <small class="mb-0">Create responsive banner sections by adding columns. Total width should equal 12.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif



    <!-- Builder Columns -->
    <div wire:sortable="updateColumnOrder" class="banner-columns">
        @foreach ($columns as $i => $column)
        <div wire:sortable.item="{{ $i }}" wire:key="column-{{ $i }}" class="banner-column-card">
            <div class="border-0 shadow-sm card">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <i wire:sortable.handle class="mr-2 cursor-move fa fa-grip-vertical text-muted" title="Drag to reorder"></i>
                        <h6 class="mb-0 small">
                            <i class="mr-1 fa fa-layer-group text-primary"></i>
                            Column {{$i + 1}}
                        </h6>
                        <span class="badge badge-{{$column['image'] ? 'success' : 'warning'}} ml-2 badge-sm">
                            {{$column['image'] ? 'Image' : 'No Image'}}
                        </span>
                    </div>
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="duplicateColumn({{$i}})" title="Duplicate Column">
                            <i class="fa fa-copy"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger" wire:click="removeColumn({{$i}})" title="Remove Column" onclick="return confirm('Are you sure you want to remove this column?')">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Image Section -->
                        <div class="mb-2 col-lg-4">
                            @if ($column['image'])
                            <div class="image-preview-container position-relative">
                                <label class="form-label">
                                    <i class="mr-1 fa fa-image"></i>Image
                                </label>
                                <div class="image-preview-wrapper">
                                                                        <img src="{{asset($column['image'])}}" alt="Banner Image" class="rounded border img-fluid">
                                    <div class="image-overlay">
                                        <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#single-picker" title="Change Image">
                                            <i class="fa fa-edit"></i>
                                        </button>
                                        <button type="button" class="ml-1 btn btn-sm btn-danger" wire:click="removeImage({{$i}})" title="Remove Image">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                <input type="hidden" name="data[columns][image_src][]" value="{{$column['image']}}">
                                <input type="hidden" name="data[columns][image][]" value="{{$column['image']}}" id="base-image-{{$i}}">
                            </div>
                            @else
                            <div class="image-upload-section">
                                <label class="form-label">
                                    <i class="mr-1 fa fa-image"></i>Image
                                </label>
                                <div class="text-center rounded border border-dashed image-upload-placeholder">
                                    <i class="mb-1 fa fa-cloud-upload-alt text-muted"></i>
                                    <p class="mb-1 text-muted small">No image</p>
                                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#single-picker">
                                        <i class="mr-1 fa fa-image"></i>Choose
                                    </button>
                                </div>
                                <div id="preview-{{$i}}" class="base_image-preview d-none">
                                    <input type="hidden" name="data[columns][image_src][]" value="{{$column['image']}}">
                                    <input type="hidden" name="data[columns][image][]" value="{{$column['image']}}" id="base-image-{{$i}}">
                                </div>
                            </div>
                            @endif
                        </div>

                                                <!-- Settings Section -->
                        <div class="col-lg-8">
                            <div class="row">
                                <!-- Width Field -->
                                <div class="mb-2 col-md-6">
                                    <label for="banner-width-{{$i}}" class="form-label">
                                        <i class="mr-1 fa fa-arrows-alt-h"></i>Width
                                    </label>
                                    <div class="input-group input-group-sm">
                                        <x-input
                                            name="data[columns][width][]"
                                            wire:model.live="columns.{{$i}}.width"
                                            value="{{ old('data.columns.width.'.$i) }}"
                                            id="banner-width-{{$i}}"
                                            placeholder="1-12"
                                            min="1"
                                            max="12"
                                            type="number"
                                            class="form-control form-control-sm"
                                        />
                                        <div class="input-group-append">
                                            <span class="input-group-text">/12</span>
                                        </div>
                                    </div>
                                    <small class="form-text text-muted">Grid columns (1-12)</small>
                                    <x-error field="data[columns][width][]" />
                                </div>

                                <!-- Animation Field -->
                                <div class="mb-2 col-md-6">
                                    <label for="banner-animation-{{$i}}" class="form-label">
                                        <i class="mr-1 fa fa-magic"></i>Animation
                                    </label>
                                    <select
                                        name="data[columns][animation][]"
                                        wire:model="columns.{{$i}}.animation"
                                        class="form-control form-control-sm custom-select"
                                        id="banner-animation-{{$i}}"
                                    >
                                        @foreach (['fade-left', 'fade-right', 'fade-up', 'fade-down'] as $animation)
                                            <option value="{{$animation}}" {{$column['animation'] == $animation ? 'selected' : ''}}>
                                                {{ucwords(str_replace('-', ' ', $animation))}}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">Entrance animation</small>
                                    <x-error field="animation" />
                                </div>

                                <!-- Link Field -->
                                <div class="mb-2 col-md-6">
                                    <label for="banner-link-{{$i}}" class="form-label">
                                        <i class="mr-1 fa fa-link"></i>Link URL
                                    </label>
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fa fa-globe"></i></span>
                                        </div>
                                        <x-input
                                            name="data[columns][link][]"
                                            wire:model="columns.{{$i}}.link"
                                            value="{{ old('data.columns.link.'.$i) }}"
                                            id="banner-link-{{$i}}"
                                            placeholder="URL or path"
                                            class="form-control form-control-sm"
                                        />
                                    </div>
                                    <small class="form-text text-muted">Optional link destination</small>
                                    <x-error field="data[columns][link][]" />
                                </div>

                                <!-- Categories Field -->
                                <div class="mb-2 col-md-6">
                                    <label for="banner-categories-{{$i}}" class="form-label">
                                        <i class="mr-1 fa fa-tags"></i>Categories
                                    </label>
                                    <x-category-dropdown
                                        :categories="$categories"
                                        name="data[columns][categories][{{$i}}][]"
                                        placeholder="Select categories..."
                                        id="banner-categories-{{$i}}"
                                        multiple="true"
                                        :selected="old('data.columns.categories.'.$i, $column['categories'] ?? [])"
                                    />
                                    <small class="form-text text-muted">Related product categories</small>
                                    <x-error field="data[columns][categories][{{$i}}][]" class="d-block" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

        <!-- Add Column Button -->
    <div class="mt-2 row">
        <div class="col-12">
            @if(empty($columns) || !(end($columns) ?: ['image' => null])['image'] == null)
            <div class="text-center">
                <button type="button" class="btn btn-success" wire:click="addColumn">
                    <i class="mr-1 fa fa-plus"></i>Add Column
                </button>
                <p class="mt-1 mb-0 text-muted">
                    <small>
                        <i class="mr-1 fa fa-lightbulb"></i>
                        Add multiple columns for carousel or side-by-side layout.
                    </small>
                </p>
            </div>
            @endif
        </div>
    </div>

    <!-- Grid Calculator Helper & Quick Actions -->
    @if(!empty($columns))
    <div class="mt-2 row">
        <div class="col-12">
            <div class="border-0 card bg-light">
                <div class="py-2 card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <span class="mr-2 text-muted small">
                                    <i class="mr-1 fa fa-calculator"></i>Grid:
                                </span>
                                <span class="small text-dark">
                                    Total:
                                    <span class="badge badge-{{array_sum(array_column($columns, 'width')) == 12 ? 'success' : (array_sum(array_column($columns, 'width')) > 12 ? 'danger' : 'warning')}} ml-1">
                                        {{array_sum(array_column($columns, 'width'))}}/12
                                    </span>
                                </span>
                                @if(array_sum(array_column($columns, 'width')) != 12)
                                <button type="button" class="ml-2 btn btn-sm btn-outline-primary" wire:click="normalizeWidths" title="Auto-adjust widths">
                                    <i class="fa fa-magic"></i>
                                </button>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6 text-md-right">
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-outline-info" onclick="previewAnimations()" title="Preview animations">
                                    <i class="fa fa-play"></i>
                                </button>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-toggle="dropdown">
                                        <i class="fa fa-magic"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <button type="button" class="dropdown-item" wire:click="setLayout('full')">
                                            <i class="mr-2 fa fa-square"></i>Full (12)
                                        </button>
                                        <button type="button" class="dropdown-item" wire:click="setLayout('half')">
                                            <i class="mr-2 fa fa-th-large"></i>Half (6+6)
                                        </button>
                                        <button type="button" class="dropdown-item" wire:click="setLayout('third')">
                                            <i class="mr-2 fa fa-th"></i>Third (4+4+4)
                                        </button>
                                        <button type="button" class="dropdown-item" wire:click="setLayout('asymmetric')">
                                            <i class="mr-2 fa fa-th-list"></i>Asymmetric (8+4)
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
