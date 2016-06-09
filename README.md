![Logo](https://github.com/EFTEC/BladeOne/blob/gh-pages/images/bladelogo.png)

# BladeOne
BladeOne is a standalone version of Blade Template Engine that uses a single php file and can be ported and used in different projects.

## Introduction (From Laravel webpage)

Blade is the simple, yet powerful templating engine provided with Laravel. Unlike other popular PHP templating engines, Blade does not restrict you from using plain PHP code in your views. All Blade views are compiled into plain PHP code and cached until they are modified, meaning Blade adds essentially zero overhead to your application. Blade view files use the .blade.php file extension and are typically stored in the resources/views directory.

## About this version

By standard, Blade is part of Laravel (Illuminate components) and for to use it, you requires to install Laravel and Illuminate-view components.
Blade as a template engine is pretty nice and clear. Also it generates a (some that) clean code. And its starting to be considered a de-facto template system for php (Smarty has been riding off the sunset since years ago). So, if we are able to use it without Laravel then its a big plus for many projects. In fact, in theory its is even possible to use with Laravel.
Exists different version of Blade Template that runs without Laravel but most requires 50 or more files and those templates add a new level of complexity:

- More files to manages.
- Changes to the current project (if you want to integrate the template into an existent one)
- Incompatibilities amongst other projects.
- Slowness (if your server is not using op-cache)
- Most of the code in the original Blade is used for future use, including the chance to use a different template engine.
- Some Laravel legacy code.

This project uses a single file called BladeOne.php and a single class (called BladeOne). If you want to use it then include it, creates the folders and that's it!. Nothing more (not even namespaces)*[]: 

## Usage

example.php:
```php
<?php
include "BladeOne.php";

$views = __DIR__ . '/views';
$cache = __DIR__ . '/cache';
$blade=new BladeOne($views,$cache);
echo $blade->run("hello",array("variable1"=>"value1"));
```

Where $views is the folder where the views (templates not compiled) will be stored. 
$cache is the folder where the compiled files will be stored.

In this example, the BladeOne opens the template **hello**. So in the views folders it should exists a file called **hello.blade.php**

views/hello.blade.php:
```html
<h1>Title</h1>
{{$variable1}}
```

## Template tags

### Template Inheritance

#### In the master page (layout)
|Tag|Note|status|
|---|---|---|
|@section('sidebar')|Start a new section|0.2b ok|
|@show|Indicates where the content of section will be displayed|0.2 ok|
|@yield('title')|Show here the content of a section|0.2b ok|

#### Using the master page (using the layout)
|Tag|Note|status|
|---|---|---|
|@extends('layouts.master')|Indicates the layout to use|0.2b ok|
|@section('title', 'Page Title')|Sends a single text to a section|0.2b ok|
|@section('sidebar')|Start a block of code to send to a section|0.2b ok|
|@endsection|End a block of code|0.2b ok|
|@parent|Show the original code of the section|0.2b ok|

### variables


|Tag|Note|status|
|---|---|---|
|{{$variable1}}|show the value of the variable using htmlentities (avoid xss attacks)|0.2b ok|
|@{{$variable1}}|show the value of the content directly (not evaluated, useful for js)|0.2b ok|
|{!!$variable1!!}|show the value of the variable without htmlentities (no escaped)|0.2b ok|
|{{ $name or 'Default' }}|value or default|0.2b ok|

### logic

|Tag|Note|status|
|---|---|---|
|@if (boolean)|if logic-conditional|0.2b ok|
|@elseif (boolean)|else if logic-conditional|0.2b ok|
|@else|else logic|0.2b ok|
|@endif|end if logic|0.2b ok|
|@unless(boolean)|execute block of code is boolean is false|0.2b ok|

### loop

|Tag|Note|status|
|---|---|---|
|@for($i = 0; $i < 10; $i++)|for loop|0.2b ok|
|@endfor|end of for loop|0.2b ok|
|@foreach($array as $obj)|foreach loop|0.2b ok|
|@endforeach|end of foreach loop|0.2b ok|
|@forelse($array as $obj)|inverse foreach loop|not tested|
|@empty|if forelse loop is empty the executes the next block|not tested|
|@endforelse|end of forelse block|not tested|
|@while(boolean)|while loop|not tested|
|@endwhile|end while loop|not tested|

##Sub Views

|Tag|Note|status|
|---|---|---|
|@include('folder.template')|Include a template|0.2b ok|
|@include('folder.template',['some' => 'data'])|Include a template with new variables|0.2b ok|
|@each('view.name', $array, 'variable')|Includes a template for each element of the array|0.2b ok|
Note: Templates called folder.template is equals to folder/template

##Comments

|Tag|Note|status|
|---|---|---|
|{{-- text --}}|Include a comment|0.2b ok|

##Stacks
|Tag|Note|status|
|---|---|---|
|@push('elem')|Add the next block to the push stack|0.2b ok|
|@endpush|End the push block|0.2b ok|
|@stack('elem')|Show the stack|0.2b ok|

##Service Inject
|Tag|Note|status|
|---|---|---|
|@inject('metrics', 'App\Services\MetricsService')|Used for insert a Laravel Service|NOT SUPPORTED|

##Extending Blade

Not compatible with the extension of Laravel's Blade.

## Examples

https://laravel.com/docs/master/blade


##Todo

Some features are missing , with bugs or not tested  but the basic ones are working.
@each could be optimized.
There are several tags of undocumented features of the original Blade code.
Extending BladeOne opens a world of opportunities. **May be a bladeone-bootstrap3 class in the future.**

##Version

2016-06-08 0.2. Beta First publish launch.

=======

##Collaboration

You are welcome to use it, share it, ask for changes and whatever you want to. Just keeps the copyright notice in the file.

##Future
I checked the code of BladeOne and i know that there are a lot of room for improvement.


##License
MIT License.
BladeOne (c) 2016 Jorge Patricio Castro Castillo
Blade (c) 2012 Laravel Team (This code is based and use  the work of the team of Laravel.)
