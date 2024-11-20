<ul class="space {{ $space ? '' : 'first-space' }}" id="space-{{ $space }}" data-space="{{ $space }}">
    @foreach($categories as $item)
    <li id="space-item-{{ $item->id }}" class="route space-item-{{ $item->id }}" data-parent="{{ $item->parent->id ?? 0 }}" data-order="{{ $item->order }}">
        <h5 class="title" id="title-{{ $item->id }}">{{ $item->category->name }}</h5>
        <span class="ui-icon ui-icon-arrow-4-diag"></span>
        <button type="button" data-id="{{ $item->id }}" class="delete-item btn btn-sm btn-danger">x</button>
        <x-category-menu :categories="$item->childrens" :space="$item->id" />
    </li>
    @endforeach
</ul>
