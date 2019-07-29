@extends('bug.base')

@section('content')
    @@wppost

    <h1>@@title</h1>

    <button id="init">Init</button>

    <div id="app"></div>

    <a href="@@acf('link')">acf link</a>

    @@content

    @@wpendpost
@endsection