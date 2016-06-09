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

## Why do you need a framework for php?

Simple, let's consider the next code:

_php code:_
```php
<h1>Example</h1>
<form>
    <label>Field</label><input type='text' value='<?php echo htmlentities($name); ?>' name='name' /><br>
    <?php if($name=='') {?>
    Name missing<br>
    <?php } ?>
    <?php for($i=0;$i<10;$i++) {?>
    <?php echo htmlentities($i);?><br>
    <?php } ?>
</form>
```

Using BladeOne you get:
```php
<h1>Example</h1>
<form>
    <label>Field</label><input type='text' value='{{$name}}' name='name' /><br>
    @if($name=='')
    Name missing<br>
    @endif
    @for($i;$i<10;$i++)
    {{$i}}<br>
    @endfor
</form>
```

That its short and easy to understand.
Also, it separates the business layer with the visual layer so instead of:

_php code_ (
```php
<?php
// here my php code
$value=@$_GET['id'];
$obj=new Class();
...
?><html>
<h1>Subtitle</h1>
<div>Hello World</div>
</html>
?>
here more php code.
<?
```

_we should do_
```php
<?php
// here my php code
$value=@$_GET['id'];
$obj=new Class();
$bladeone->run('view',array());
here more php code.
<?
```

_and view.blade.php:_
```html
<html>
<h1>Subtitle</h1>
<div>Hello World</div>
</html>
```


## Usage

> You only need this file: **BladeOne.php** (and don't forget the license file)


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

You also can force the compilation of the code by adding a true as the third parameter.

```php
echo $blade->run("hello",array("variable1"=>"value1"),true);
```

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
|@section('sidebar')|Start a new section|0.2b OK|
|@show|Indicates where the content of section will be displayed|0.2b OK|
|@yield('title')|Show here the content of a section|0.2b OK|

#### Using the master page (using the layout)
|Tag|Note|status|
|---|---|---|
|@extends('layouts.master')|Indicates the layout to use|0.2b OK|
|@section('title', 'Page Title')|Sends a single text to a section|0.2b OK|
|@section('sidebar')|Start a block of code to send to a section|0.2b OK|
|@endsection|End a block of code|0.2b OK|
|@parent|Show the original code of the section|REMOVED(*)|

Note :(*) This feature is in the original documentation but its not implemented neither its required. May be its an obsolete feature.


### variables


|Tag|Note|status|
|---|---|---|
|{{$variable1}}|show the value of the variable using htmlentities (avoid xss attacks)|0.2b OK|
|@{{$variable1}}|show the value of the content directly (not evaluated, useful for js)|0.2b OK|
|{!!$variable1!!}|show the value of the variable without htmlentities (no escaped)|0.2b OK|
|{{ $name or 'Default' }}|value or default|0.2b OK|

### logic

|Tag|Note|status|
|---|---|---|
|@if (boolean)|if logic-conditional|0.2b OK|
|@elseif (boolean)|else if logic-conditional|0.2b OK|
|@else|else logic|0.2b OK|
|@endif|end if logic|0.2b OK|
|@unless(boolean)|execute block of code is boolean is false|0.2b OK|

### loop

|Tag|Note|status|
|---|---|---|
|@for($i = 0; $i < 10; $i++)|for loop|0.2b OK|
|@endfor|end of for loop|0.2b OK|
|@foreach($array as $obj)|foreach loop|0.2b OK|
|@endforeach|end of foreach loop|0.2b OK|
|@forelse($array as $obj)|inverse foreach loop|0.2b OK|
|@empty|if forelse loop is empty the executes the next block|0.2b OK|
|@endforelse|end of forelse block|0.2b OK|
|@while(boolean)|while loop|0.2b OK|
|@endwhile|end while loop|0.2b OK|

### Sub Views

|Tag|Note|status|
|---|---|---|
|@include('folder.template')|Include a template|0.2b OK|
|@include('folder.template',['some' => 'data'])|Include a template with new variables|0.2b OK|
|@each('view.name', $array, 'variable')|Includes a template for each element of the array|0.2b OK|
Note: Templates called folder.template is equals to folder/template

### Comments

|Tag|Note|status|
|---|---|---|
|{{-- text --}}|Include a comment|0.2b OK|

### Stacks
|Tag|Note|status|
|---|---|---|
|@push('elem')|Add the next block to the push stack|0.2b OK|
|@endpush|End the push block|0.2b OK|
|@stack('elem')|Show the stack|0.2b OK|

### Service Inject
|Tag|Note|status|
|---|---|---|
|@inject('metrics', 'App\Services\MetricsService')|Used for insert a Laravel Service|NOT SUPPORTED|

### Extending Blade

The classes BladeOneHtml and BladeOneLogic are a working example of extending the classes.

#### How to extends BladeOne
1) Add a new class that (ahem) extends the BladeOne class.

BladeOneHtml.php:
```php
<?php
class BladeOneHtml extends BladeOne
{
}
```

2) Add a member to this class with the name starting with "compile" while the rest of the name is the tag used. This function should has a single parameter. This function should returns a string (the string will be saved in the compiled template).

inside the extended class:
```php
public function compileSelectOneMenu($expression) {
```

usage:
```html
@selectonemenu('hello')
```


## New Tags added by BladeOneHtml (Only for BladeOne)

For using this tag, the code requires to use the class BladeOneHtml



### Select

```html
@selectonemenu('id1')
    @selectitem('0','--Select a country--')
    @selectitems($countries,'id','name',$countrySelected)
@endselectonemenu()
```

@selectonemenu creates the **select** tag. The first value is the id and name of the tag.
@selectitem allows to add one element **option** tag. The first value is the id and the second is the visible text
@selectitems allows to add one list of elements **option** tag. The first value is the list of values, the second and third is the id and name. And the fourth one is the selected value (optional)
    
![select](http://i.imgur.com/yaMavQB.jpg?1)

### Input

```html
@input('iduser',$currentUser,'text'[,$extra])
```

@input creates a **input** tag. The first value is the id/name, the second is the default value, the third is the type (by default is text for textbox).

![input](http://i.imgur.com/pyiwEg7.jpg)

### Button

```html
@commandbutton('iduser','value','label'[,$extra])
```

@commandbutton creates a **input** tag. The first value is the id/name, the second is the value, the third is the label of the button. 

![commandbutton](http://i.imgur.com/fvRzou1.jpg)

### Extra Parameter
 
Additionally, you can add an (optional) last parameter with additional value (see the example of @selectonemenu)

```html
 <!-- code using bootstrap -->
 <div class="form-group">
  <label for="sel1">Select list:</label>
        @selectonemenu('id1')
            @selectitem('0','--Select a country--',"class='form-control'")
            @selectitems($countries,'id','name',$countrySelected)
        @endselectonemenu()
  </select>
</div>
```



## Defintion of Blade Template

https://laravel.com/docs/master/blade


##Todo

- Some features are missing , with bugs or not tested  but the basic ones are working.
- @each could be optimized.
- There are several tags of undocumented features of the original Blade code.
- Extending BladeOne opens a world of opportunities. **May be a bladeone-bootstrap3 class in the future.**
- Speed up the loading of compiled templates.

##Differences between Blade and BladeOne

- Laravel's extension removed.
- Dependencies to other class removed.
- All operations use a unique class. Not more Arr and Str classes
- The engine is self contained.
- Setter and Getters removed. Instead, we are using the PHP style (public members). 
- Some of the logic of the code was changed.


##Version

- 2016-06-09 1.0 Version. Most works. Added extensions and error control with a tag is not defined.
- 2016-06-08 0.2. Beta First publish launch.


=======

##Collaboration

You are welcome to use it, share it, ask for changes and whatever you want to. Just keeps the copyright notice in the file.


##Future
I checked the code of Laravel's Blade and i know that there are a lot of room for improvement.


##License
MIT License.
BladeOne (c) 2016 Jorge Patricio Castro Castillo
Blade (c) 2012 Laravel Team (This code is based and use the work of the team of Laravel.)
