
some text...<br>
@section('js_bottom')
    @parent
    @include('widgets.ckeditor', ['textarea_id' => 'information'])
@endsection