--- calling embed ---
@embed('embed.component')
    @section('title', 'Warning panel 1')
    @section('content')
        <hr>
        <strong>Warning panel content 1</strong>
        <hr>
    @stop
@endembed

--- calling embed 2---
@embed('embed.component')
@section('title', 'Warning panel 2')
@section('content')
    <hr>
    <strong>Warning panel content 2</strong>
    <hr>
@stop
@endembed