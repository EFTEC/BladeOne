@extends('layouts.mylayout')

@section('title', $title)

@section('content')
    @parent
    {{$content}}
@endsection
