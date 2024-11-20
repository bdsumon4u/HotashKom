<textarea
    name="{{ $name }}"
    id="{{ $id }}"
    rows="{{ $rows }}"
    {{ $attributes->merge([
        'class' => 'form-control '.($errors->has($name) ? 'is-invalid' : ''),
    ]) }}
>{{ old($name, $slot) }}</textarea>
