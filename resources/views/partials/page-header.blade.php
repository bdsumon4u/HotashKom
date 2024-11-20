<div class="page-header">
    <div class="page-header__container container">
        <div class="page-header__breadcrumb">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    @foreach($paths as $url => $name)
                    <li class="breadcrumb-item"><a href="{{ $url }}">{{ $name }}</a>
                        <svg class="breadcrumb-arrow" width="6px" height="9px">
                            <use xlink:href="{{ asset('strokya/images/sprite.svg#arrow-rounded-right-6x9') }}"></use>
                        </svg>
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