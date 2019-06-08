<h1>Testing extend 2</h1>
@extends('simpleextend.extendme2')

@section("section1")
    <h2>section 1 replaced $content=[{{$content}}]</h2>
@endsection()

@section("section2")
    <h2>section 2 replaced $content=[{{$content}}]</h2>
@endsection()
