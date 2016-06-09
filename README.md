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
|...more|...|...|

##Todo

Some features are missing or wrong but the basic ones are working.

##Version

2016-06-08 0.2. Beta First publish launch.
<<<<<<< Updated upstream
=======

##Collaboration

You are welcome to use it, share it, ask for changes and whatever you want to. Just keeps the copyright notice in the file.

##Future
I checked the code of blade and i know that there are a lot of room for improvement.


##License
MIT License.
This code is based in the work of the team of Laravel (is also MIT but i don't find the license file)
>>>>>>> Stashed changes
