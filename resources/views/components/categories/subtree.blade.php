<ul class="space" id="space-{{$space}}" data-space="{{$space}}">
    @foreach($childrens as $children)
        <li id="space-item-{{ $children->id }}" data-parent="{{ $children->parent_id ?? 0 }}" data-order="{{ $children->order }}" class="route space-item-{{ $children->id }} {{ request('active_id', 0) == $children->id ? 'active' : '' }}">
            <a class="title" href="?active_id={{ $children->id }}">{{ $children->name }} @unless ($children->is_enabled) <i class="fa fa-times text-danger"></i> @endunless</a>
            <span class="ui-icon ui-icon-arrow-4-diag"></span>
            <button type="button" data-id="{{ $children->id }}" class="delete-item btn btn-sm btn-danger">x</button>
            @include('components.categories.subtree', ['childrens' => $children->childrens, 'depth' => $depth + 1, 'space' => $children->id])
        </li>
    @endforeach
</ul>
