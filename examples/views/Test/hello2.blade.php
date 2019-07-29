@extends('Shared.newlayout')


@section('footer')
    <p style="background-color: darkgrey">It is the footer. This footer is yield so it shouldn't contain a parent
        @parent this parent is completely ignored.
    </p>
@endsection

