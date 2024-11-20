<select selector name="{{ $name }}" placeholder="{{ $placeholder ?? '' }}" data-placeholder="{{ $placeholder ?? '' }}" id="{{ $id ?? '' }}" class="form-control" {{ ($multiple ?? false) == 'true' ? 'multiple' : '' }}>
    @if(($multiple ?? false) != 'true')    
    <option value="">{{ $placeholder }}</option>
    @endif
    @foreach($categories as $category)
        <option value="{{ $category->id }}" @if(is_array($selected)) {{ in_array($category->id, $selected) ? 'selected' : '' }} @else {{ $selected == $category->id ? 'selected' : '' }} @endif @if($disabled == $category->id) disabled @endif>{{ $category->name }}</option>
        @includeWhen(isset($category->childrens), 'components.categories.childrens', ['childrens' => $category->childrens, 'depth' => 1])
    @endforeach
</select>