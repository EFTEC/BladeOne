@component()

@set($type=  'info')

<div class="panel panel-t1">
    <h5>@yield('title', 'default title') <small>@yield('subtitle')</small></h5>
    <div>
        @yield('content')
    </div>
</div>

@endcomponent