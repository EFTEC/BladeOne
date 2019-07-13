@extends('Shared.newlayout')


@section('footer')
    <p style="background-color: darkgrey">It is the footer. This footer is yield so it shouldn't contain a parent
        @parent this parent is completely ignored.
    </p>
@endsection

@section('footermissing')
    <p style="background-color: darkgrey">If you are reading this, then footermissing is not missing.
    </p>
    <p>@@parent Don't add this one.</p> 
@endsection