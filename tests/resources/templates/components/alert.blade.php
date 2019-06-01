<div class="alert">
    @isset($title)
        <h2>{{ $title }}</h2>
    @endisset
    {{ $slot }}
</div>