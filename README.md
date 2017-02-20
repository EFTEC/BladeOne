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
Use eftec\bladeone;

$views = __DIR__ . '/views';
$cache = __DIR__ . '/cache';
define("BLADEONE_MODE",1); // (optional) 1=forced (test),2=run fast (production), 0=automatic, default value.
$blade=new bladeone\BladeOne($views,$cache);
echo $blade->run("hello",array("variable1"=>"value1"));
```

_Or using composer's autoload.php_
```php
<?php
require "vendor/autoload.php";

Use eftec\bladeone;

$views = __DIR__ . '/views';
$cache = __DIR__ . '/cache';
define("BLADEONE_MODE",1); // (optional) 1=forced (test),2=run fast (production), 0=automatic, default value.
$blade=new bladeone\BladeOne($views,$cache);
echo $blade->run("hello",array("variable1"=>"value1"));
```
_(modify composer.json as follow) and run "composer update"_
```json
"autoload": {
  "psr-4": {
    "eftec\\": "vendor/eftec/"
  }
}
```  

Where $views is the folder where the views (templates not compiled) will be stored. 
$cache is the folder where the compiled files will be stored.

In this example, the BladeOne opens the template **hello**. So in the views folders it should exists a file called **hello.blade.php**

views/hello.blade.php:
```html
<h1>Title</h1>
{{$variable1}}
```

## Business Logic/Controller methods

### constructor
```php
$blade=new bladeone\BladeOne($views,$cache);
```
- BladeOne(templatefolder,compiledfolder) Creates the instance of BladeOne.
-   templatefolders indicates the folder (without ending backslash) of where the template files (*.blade.php) are located.
-   compiledfolder indicates the folder where the result of files will be saves. This folder should has write permission. Also, this folder could be located outside of the Web Root.



### run
```php
echo $blade->run("hello",array("variable1"=>"value1"));
```
- run([template,[array])  Runs the template and generates a compiled version (if its required), then it shows the result.
-   template is the template to open. The dots are used for to separate folders.  If the template is called "folder.example" then the engine tries to open the file "folder\example.blade.php"
-   array (optional). Indicates the values to use for the template.  For example ['v1'=>10'], indicates the variable $v1 is equals to 10

### BLADEONE_MODE (global constant) (optional)
```php
define("BLADEONE_MODE",1); // (optional) 1=forced (test),2=run fast (production), 0=automatic, default value.
```
- BLADEONE_MODE Is a global constant that defines the behaviour of the engine.
-   1=forced. Indicates that the engine always will compile the template.
-   2=fast. Indicates that the engine always will use the compiled version


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
|@parent|Show the original code of the section|REMOVED(*)|

Note :(*) This feature is in the original documentation but its not implemented neither its required. May be its an obsolete feature.

### variables
|Tag|Note|status|
|---|---|---|
|{{$variable1}}|show the value of the variable using htmlentities (avoid xss attacks)|0.2b ok|
|@{{$variable1}}|show the value of the content directly (not evaluated, useful for js)|0.2b ok|
|{!!$variable1!!}|show the value of the variable without htmlentities (no escaped)|0.2b ok|
|{{ $name or 'Default' }}|value or default|0.2b ok|
|{{Class::StaticFunction($variable)}}|call and show a function (the function should return a value)|0.2b ok|

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

### Sub Views
|Tag|Note|status|
|---|---|---|
|@include('folder.template')|Include a template|0.2b ok|
|@include('folder.template',['some' => 'data'])|Include a template with new variables|0.2b ok|
|@each('view.name', $array, 'variable')|Includes a template for each element of the array|0.2b ok|
Note: Templates called folder.template is equals to folder/template

### Comments
|Tag|Note|status|
|---|---|---|
|{{-- text --}}|Include a comment|0.2b ok|

### Stacks
|Tag|Note|status|
|---|---|---|
|@push('elem')|Add the next block to the push stack|0.2b ok|
|@endpush|End the push block|0.2b ok|
|@stack('elem')|Show the stack|0.2b ok|

### @set (new for 1.5)
@set($variable=[value])
@set($variable) is equals to @set($variable=$variable+1)
- $variable define the variable to add. If not value is defined the it adds +1 to a variable.
- value (option) define the value to use.

### Service Inject
|Tag|Note|status|
|---|---|---|
|@inject('metrics', 'App\Services\MetricsService')|Used for insert a Laravel Service|NOT SUPPORTED|

### Extending Blade
Not compatible with the extension of Laravel's Blade.

## Extensions Libraries (optional) 
[BladeOneHtml Documentation](BladeOneHtml.md)

[BladeOneLogic Documentation](BladeOneLogic.md)

[BladeOneCache Documentation](BladeOneCache.md)

## Definition of Blade Template
https://laravel.com/docs/5.2/blade

##Differences between Blade and BladeOne

- Laravel's extension removed.
- Dependencies to other class removed (around 30 classes).
- The engine is self contained.
- Setter and Getters removed. Instead, we are using the PHP style (public members).
- BladeOne doesn't support static calls.

## Differences between Blade+Laravel and BladeOne+BladeOneHTML

Instead of use the Laravel functions, for example Form::select
```html
{{Form::select('countryID', $arrayCountries,$countrySelected)}}
```

We have native tags as @select,@item,@items and @endselect
```html
@select('countryID')
    @item('0','--Select a country--',$countrySelected)
    @items($arrayCountries,'id','name',$countrySelected)
@endselect()
```

This new syntaxis add an (optionally) a non-selected row.
Also, BladeOneHTML adds multiple select, fixed values (without array), grouped select and many more.

##Todo

- ~~- Some features are missing , with bugs or not tested  but the basic ones are working.~~ DONE
- ~~- @each could be optimized.~~ DONE
- ~~- There are several tags of undocumented features of the original Blade code.~~  DONE
- ~~- Extending BladeOne opens a world of opportunities.~~ 
- ~~  May be a bladeone-bootstrap3 class in the future.~~ Done  
- ~~- Speed up the loading of compiled templates.~~  DONE

##Version

- 2016-06-08 0.2. Beta First publish launch.
- 2016-06-09 1.0 Version. Most works. Added extensions and error control with a tag is not defined.
- 2016-06-09 1.1 Some fine tune.
- 2016-06-10 1.2 New changes.  Added namespaces (for autocomplete and compatibility with composer)
- 2016-06-12 1.3 Lots of clean up. I removed some unused parameters.  I fixed a problem with forced in BladeOne.  I separates the doc per extension.
- 2016-06-24 1.4 Updates extensions.  Now it uses strut instead of classes. Added a new extension BladeOneCache.
- 2016-07-03 1.5 New features such as **@set** command
- 2016-08-14 1.6 Some cleanups. Add new documentation   

=======

##Collaboration

You are welcome to use it, share it, ask for changes and whatever you want to. Just keeps the copyright notice in the file.

##Future
I checked the code of BladeOne and i know that there are a lot of room for improvement.


##License
MIT License.
BladeOne (c) 2016 Jorge Patricio Castro Castillo
Blade (c) 2012 Laravel Team (This code is based and use  the work of the team of Laravel.)
