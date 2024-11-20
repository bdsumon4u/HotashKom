@foreach($childrens as $children)
    <option value="{{ $children->id }}" @if(is_array($selected)) {{ in_array($children->id, $selected) ? 'selected' : '' }} @else {{ $selected == $children->id ? 'selected' : '' }} @endif @if($disabled == $category->id) disabled @endif> @for($i = $depth; $i; $i--) | -- @endfor {{ $children->name }}</option>
    @include('components.categories.childrens', ['childrens' => $children->childrens, 'depth' => $depth + 1])
@endforeach