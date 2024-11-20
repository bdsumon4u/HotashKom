@push('styles')
    <style>
        #colorPick {
            background: rgba(255,255,255,1);
        }
        .picker {
            border-radius: 5px;
            border: 1px solid #dedede;
            padding: 5px;
            cursor: pointer;
            -webkit-transition: all linear .2s;
            -moz-transition: all linear .2s;
            -ms-transition: all linear .2s;
            -o-transition: all linear .2s;
            transition: all linear .2s;
        }
    </style>
@endpush
<div class="tab-pane active" id="item-color" role="tabpanel">
    @foreach(['topbar' => 'Top Bar', 'header' => 'Header', 'navbar' => 'Nav Bar', 'search' => 'Search', 'category_menu' => 'Category Menu', 'section' => 'Section', 'badge' => 'Badge', 'footer' => 'Footer', 'primary' => 'Primary Button', 'add_to_cart' => 'AddToCart Button', 'order_now' => 'OrderNow Button'] as $id => $title)
    <div class="row border-bottom mb-2">
        <div class="col-sm-12">
            <h4><small class="border-bottom mb-1">{{ $title}}</small></h4>
        </div>
        @foreach(['background_color' => 'Background Color', 'background_hover' => 'Background Hover', 'text_color' => 'Text Color', 'text_hover' => 'Text Hover'] as $key => $label)
        <div class="col-md-3">
            <div class="form-group">
                <label for="">{{ $label }}</label>
                <div class="picker" data-initialcolor="{{$color->$id->$key ?? null}}">
                    <x-input type="text" name="color[{{ $id }}][{{ $key }}]" :value="$color->$id->$key ?? null" class="w-75 ml-auto" />
                </div>
                <x-error field="color.{{ $id }}.{{ $key }}" />
            </div>
        </div>
        @endforeach
    </div>
    @endforeach
</div>

@push('scripts')
<script>
    $(".picker").colorPick({
        'allowRecent': true,
        'recentMax': 5,
        'allowCustomColor': true,
        'palette': ["#1abc9c", "#16a085", "#2ecc71", "#27ae60", "#3498db", "#2980b9", "#9b59b6", "#8e44ad", "#34495e", "#2c3e50", "#f1c40f", "#f39c12", "#e67e22", "#d35400", "#e74c3c", "#c0392b", "#ecf0f1", "#bdc3c7", "#95a5a6", "#7f8c8d"],
        'onColorSelected': function() {
            this.element.css({'backgroundColor': this.color, 'color': this.color});
            this.element.children('input').val(this.color);
        }
    });
</script>
@endpush
