<ul class="space first-space" id="space-0" data-space="0">
    @foreach($categories as $category)
        @php
            $href = '#';
            if (! $category instanceof \App\Option) {
                $href = '?active_id='.$category->id;
            }
        @endphp
        <li id="space-item-{{ $category->id }}" data-parent="{{ $category->parent_id ?? 0 }}" data-order="{{ $category->order }}" class="route space-item-{{ $category->id }} {{ request('active_id', 0) == $category->id ? 'active' : '' }}">
            <a class="title" href="{{ $href }}">{{ $category->name }} @unless ($category->is_enabled) <i class="fa fa-times text-danger"></i> @endunless</a>
            <span class="ui-icon ui-icon-arrow-4-diag"></span>
            <button type="button" data-id="{{ $category->id }}" class="delete-item btn btn-sm btn-danger">x</button>
            @includeWhen(isset($category->childrens), 'components.categories.subtree', ['childrens' => $category->childrens, 'depth' => 1, 'space' => $category->id])
        </li>
    @endforeach
</ul>
