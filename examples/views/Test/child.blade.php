<!-- Stored in resources/views/child.blade.php -->

@extends('Test.layout')

@section('title', 'Page Title')

@section('sidebar')
    @parent
    <p>This is appended to the master sidebar (from child).</p>
    @parent
    @parent
@endsection

@section('content')
    <p>This is my body content (from child).</p>
@endsection