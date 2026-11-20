<div class="mb-3 col-12" data-editor-section="{{ $editorKey }}">
    <input type="hidden" name="section_settings[{{ $editorKey }}][rows_present]" value="1">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h6 class="mb-0">{{ ucfirst($rowLabel) }} rows</h6>
        <button type="button" class="btn btn-sm btn-outline-primary"
            @click="addContentRow('{{ $editorKey }}', {{ json_encode(array_keys($rowFields)) }})"
            :disabled="contentRows.{{ $editorKey }}.length >= 100">Add {{ $rowLabel }}</button>
    </div>
    <p class="text-muted">Edit each field below. Use the arrows to change display order. Empty rows are ignored.</p>
    <div class="lp-editor-rows">
        <template x-for="(row, index) in contentRows.{{ $editorKey }}" :key="row.key">
            <details class="lp-editor-row">
                <summary x-text="'{{ ucfirst($rowLabel) }} ' + (index + 1) + (row.{{ array_key_first($rowFields) }} ? ': ' + row.{{ array_key_first($rowFields) }} : '')"></summary>
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-2" style="gap: 8px;">
                    <div class="lp-product-actions">
                        <button type="button" class="btn btn-sm btn-light"
                            @click="moveRow(contentRows.{{ $editorKey }}, index, -1)" :disabled="index === 0"
                            :aria-label="'Move {{ $rowLabel }} ' + (index + 1) + ' up'">↑</button>
                        <button type="button" class="btn btn-sm btn-light"
                            @click="moveRow(contentRows.{{ $editorKey }}, index, 1)"
                            :disabled="index === contentRows.{{ $editorKey }}.length - 1"
                            :aria-label="'Move {{ $rowLabel }} ' + (index + 1) + ' down'">↓</button>
                        <button type="button" class="btn btn-sm btn-outline-danger"
                            @click="contentRows.{{ $editorKey }}.splice(index, 1)"
                            :aria-label="'Remove {{ $rowLabel }} ' + (index + 1)">Remove</button>
                    </div>
                </div>
                <div class="row">
                    @foreach ($rowFields as $field => $config)
                        <div class="{{ $editorKey === 'size_guide' ? 'col-sm-4' : 'col-12' }} mb-2">
                            <label :for="'{{ $editorKey }}-{{ $field }}-' + row.key">{{ $config['label'] }}</label>
                            @if ($config['multiline'] ?? false)
                                <textarea :id="'{{ $editorKey }}-{{ $field }}-' + row.key" rows="3"
                                    :name="`section_settings[{{ $editorKey }}][rows][${index}][{{ $field }}]`"
                                    x-model="row.{{ $field }}" class="form-control"
                                    placeholder="{{ $config['placeholder'] }}" maxlength="{{ $config['max'] }}"></textarea>
                            @else
                                <input :id="'{{ $editorKey }}-{{ $field }}-' + row.key" type="text"
                                    :name="`section_settings[{{ $editorKey }}][rows][${index}][{{ $field }}]`"
                                    x-model="row.{{ $field }}" class="form-control"
                                    placeholder="{{ $config['placeholder'] }}" maxlength="{{ $config['max'] }}">
                            @endif
                        </div>
                    @endforeach
                </div>
            </details>
        </template>
    </div>
    <p class="mt-2 mb-0 text-muted" x-show="contentRows.{{ $editorKey }}.length === 0">No {{ $rowLabel }} rows. Add one above, or disable this section.</p>
</div>
