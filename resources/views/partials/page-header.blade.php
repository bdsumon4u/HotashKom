<div class="page-header">
    <div class="container page-header__container">
        <div class="page-header__breadcrumb">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    @foreach($paths as $url => $name)
                    @php
                        $isExternal = filter_var($url, FILTER_VALIDATE_URL) && !str_starts_with($url, config('app.url'));
                    @endphp
                    <li class="breadcrumb-item"><a href="{{ $url }}" @unless($isExternal) wire:navigate.hover @endunless>{{ $name }}</a>
                        <svg class="breadcrumb-arrow" width="6px" height="9px" viewBox="0 0 6 9"><path d="M.4 8.8c-.4-.4-.5-1-.1-1.4l3-2.9-3-2.9C-.1 1.2-.1.5.4.2c.4-.3.9-.3 1.3.1L6 4.5 1.6 8.7c-.3.4-.9.4-1.2.1z"/></svg>
                    </li>
                    @endforeach
                    <li class="breadcrumb-item active" aria-current="page">{{ $active }}</li>
                </ol>
            </nav>
        </div>
        @if(isset($page_title))
        <div class="page-header__title">
            <h1>{{ $page_title }}</h1>
        </div>
        @endif
    </div>
</div>
