@extends('Shared.newlayout')

@section('title', 'Page Title')

@section('sidebar')
    <p style="background-color: darkgrey">It is a sidebar created in hello2.blade.php. It could be used as many times as you want
    @parent
    </p>

    <hr>assets:
    @asset('aaa.js')

@endsection

@section('sidebar2')
    <p style="background-color: darkgrey">It is a sidebar2 created in hello2.blade.php. It could be used as many times as you want
        @parent
    </p>
@endsection

@section('footer')
    <p style="background-color: darkgrey">It is the footer. This footer is yield so it shouldn't contain a parent
        @parent this parent is completely ignored.
    </p>
@endsection
