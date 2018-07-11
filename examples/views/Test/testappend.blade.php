@extends('test.masterappend')

@section('sidebar')
    <p>This is appended to the master sidebar.</p>
@append

@section('content')
    <p>This is my body content.</p>
@stop