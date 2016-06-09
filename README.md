# BladeOne
BladeOne is a version of Blade Template Engine that uses a single php file.

## Introduction (From Laravel webpage)

Blade is the simple, yet powerful templating engine provided with Laravel. Unlike other popular PHP templating engines, Blade does not restrict you from using plain PHP code in your views. All Blade views are compiled into plain PHP code and cached until they are modified, meaning Blade adds essentially zero overhead to your application. Blade view files use the .blade.php file extension and are typically stored in the resources/views directory.

## About this version

By standard, Blade is part of Laravel (Illuminate componentes) and for to use it, you requires to install Laravel and Illuminate-view components.
Blade as a template engine is pretty nice and clear. Also it generates a (some that) clean code. And its starting to be considered a de-facto template system for php (Smarty has been riding off the sunset since years ago). So, if we are able to use it without Laravel then its a big plus for many projects. In fact, in theory its is even possible to use with Laravel.
Exists different version of Blade Template that runs without Laravel but most requires 50 or more files and those templates add a new level of complexity:

- More files to manages.
- Changes to the project.
- Incompatibilities amongst other projects.
- Slowness (if your server is not using op-cache)

This project uses a single file called BladeOne.php. If you want to use it then include it, creates the folders and that's it!. Nothing more (not even namespaces)*[]: 

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

##Todo

Some features are missing or wrong but the basic ones are working.