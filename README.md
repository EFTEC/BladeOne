![Logo](https://raw.githubusercontent.com/EFTEC/BladeOne/gh-pages/images/bladelogo.png)

# BladeOne Blade Template Engine
BladeOne is a standalone version of Blade Template Engine that uses a single PHP file and can be ported and used in different projects. It allows you to use blade template outside Laravel.

Бладеоне-это отдельная версия ядра Blade-шаблонов, которая использует один PHP-файл и может быть портирована и использована в различных проектах. Он позволяет использовать шаблон Blade за пределами laravel.    

[![Build Status](https://travis-ci.org/EFTEC/BladeOne.svg?branch=master)](https://travis-ci.org/EFTEC/BladeOne)
[![Packagist](https://img.shields.io/packagist/v/eftec/bladeone.svg)](https://packagist.org/packages/eftec/bladeone)
[![Total Downloads](https://poser.pugx.org/eftec/bladeone/downloads)](https://packagist.org/packages/eftec/bladeone)
[![Maintenance](https://img.shields.io/maintenance/yes/2019.svg)]()
[![composer](https://img.shields.io/badge/composer-%3E1.6-blue.svg)]()
[![php](https://img.shields.io/badge/php->5.6-green.svg)]()
[![php](https://img.shields.io/badge/php-7.x-green.svg)]()
[![CocoaPods](https://img.shields.io/badge/docs-70%25-yellow.svg)]()


NOTE: So far it's apparently the only one project that it's updated with the latest version of **Blade 5.8 (December 2019)**. It misses some commands [missing](#missing) but nothing more.

Примечание: до сих пор это, видимо, только один проект, который обновляется с последней версией ** Blade 5,8 (2019 января) **. Он пропускает некоторые команды [отсутствует](#missing), но ничего больше.  

- [BladeOne Blade Template Engine](#bladeone-blade-template-engine)
  * [laravel blade tutorial](#laravel-blade-tutorial)
  * [About this version](#about-this-version)
  * [Why to use it instead of native PHP?](#why-to-use-it-instead-of-native-php-)
    + [Separation of concerns](#separation-of-concerns)
  * [Security](#security)
  * [Easy to use](#easy-to-use)
    + [Performance](#performance)
    + [Scalable](#scalable)
  * [Install (pick one of the next one)](#install--pick-one-of-the-next-one-)
  * [Usage](#usage)
  * [Security (optional)](#security--optional-)
  * [Business Logic/Controller methods](#business-logic-controller-methods)
    + [constructor](#constructor)
    + [run](#run)
    + [setMode](#setmode)
    + [setFileExtension($ext), getFileExtension](#setfileextension--ext---getfileextension)
    + [setCompiledExtension($ext), getCompiledExtension](#setcompiledextension--ext---getcompiledextension)
    + [runString](#runstring)
    + [directive](#directive)
    + [directiveRT](#directivert)
    + [BLADEONE_MODE (global constant) (optional)](#bladeone-mode--global-constant---optional-)
  * [Template tags](#template-tags)
    + [Template Inheritance](#template-inheritance)
    + [In the master page (layout)](#in-the-master-page--layout-)
    + [Using the master page (using the layout)](#using-the-master-page--using-the-layout-)
    + [variables](#variables)
    + [logic](#logic)
    + [loop](#loop)
      - [@for($variable;$condition;$increment) / @endfor](#-for--variable--condition--increment-----endfor)
      - [@inject('variable name', 'namespace')](#-inject--variable-name----namespace--)
      - [@foreach($array as $alias) / @endforeach](#-foreach--array-as--alias-----endforeach)
      - [@forelse($array as $alias) / @empty / @endforelse](#-forelse--array-as--alias-----empty----endforelse)
      - [@while($condition) / @endwhile](#-while--condition-----endwhile)
      - [@splitforeach($nElem,$textbetween,$textend="")  inside @foreach](#-splitforeach--nelem--textbetween--textend------inside--foreach)
      - [@continue / @break](#-continue----break)
    + [switch / case](#switch---case)
    + [Sub Views](#sub-views)
  * [Comments](#comments)
    + [Stacks](#stacks)
  * [@set (new for 1.5)](#-set--new-for-15-)
    + [Service Inject](#service-inject)
  * [Asset Management](#asset-management)
    + [@asset](#asset)
    + [@resource](#resource)
    + [setBaseUrl($url)](#setbaseurlurl)
    + [getBaseUrl()](#getbaseurl)
    + [addAssetDict()](#addassetdictnameurl)
  * [Extensions Libraries (optional)](#extensions-libraries--optional-)
  * [Definition of Blade Template](#definition-of-blade-template)
  * [Differences between Blade and BladeOne](#differences-between-blade-and-bladeone)
  * [Differences between Blade+Laravel and BladeOne+BladeOneHTML](#differences-between-blade-laravel-and-bladeone-bladeonehtml)
  * [Version](#version)
    + [Changes between 2.x and 3.0](#changes-between-2x-and-30)
  * [todo](#todo)
  * [SourceGuardian](#sourceguardian)
  * [Collaboration](#collaboration)
  * [Future](#future)
  * [Missing](#missing)
  * [License](#license)


## Laravel blade tutorial

You can find some tutorials and example on the folder [Examples](examples).

## About this version
By standard, The original Blade library is part of Laravel (Illuminate components) and to use this template library, you require to install Laravel and Illuminate-view components.
The syntax of Blade is pretty nice and bright. It's based in C# Razor (another template library for C#). It's starting to be considered a de-facto standard template system for many PHP (Smarty has been riding off the sunset since years ago) so, if we can use it without Laravel then its a big plus for many projects. 
In fact, in theory, it is even possible to use with Laravel.
Exists different version of Blade Template that runs without Laravel but most requires 50 or more files and those templates add a new level of complexity, so they are not removing Laravel but hiding:

- More files to manages.
- Changes to the current project (if you want to integrate the template into an existent one)
- Incompatibilities amongst other projects.
- Slowness (if your server is not using op-cache)
- Most of the code in the original Blade is used for future use, including the chance to use a different template engine.
- Some Laravel legacy code.

This project uses a single file called BladeOne.php and a single class (called BladeOne). 
If you want to use it then include it, creates the folders and that's it!. Nothing more (not even namespaces)*[]:  It is also possible to use Blade even with Laravel or any other framework. After all, BladeOne is native, so it's possible to integrate into almost any project.

## Why to use it instead of native PHP?

### Separation of concerns
Let’s say that we have the next code

```php
//some PHP code
// some HTML code
// more PHP code
// more HTML code.
```
It leads to a mess of a code.  For example, let’s say that we oversee changing the visual layout of the page. In this case, we should change all the code and we could even break part of the programming.   
Instead, using a template system works in the next way:
```php
// some php code
ShowTemplate();
```
We are separating the visual layer from the code layer.  As a plus, we could assign a non-php-programmer in charge to edit the template, and he/she doesn’t need to touch or know our php code.
## Security
Let’s say that we have the next exercise (it’s a dummy example)
```php
$name=@$_GET['name'];
echo "my name is ".$name;
```
It could be separates as two files:
```php // index.php
$name=@$_GET['name'];
include "template.php";
```
```php 
// template.php
echo "my name is ".$name;
```
Even for this simple example, there is a risk of hacking.   How?  A user could sends malicious code by using the GET variable, such as html or even javascript. The second file should be written as follow:
```php 
 // template.php
echo "my name is ".html_entities($name);
```
html_entities should be used in every single part of the visual layer (html) where the user could injects malicious code, and it’s a real tedious work.   BladeOne does it automatically.
```php 
// template.blade.php
My name is {{$name}}
```
## Easy to use

BladeOne is focused on an easy syntax that it's fast to learn and to write, while it could keep the power of PHP.  

Let's consider the next template:

```php // template.php
<select>
    <? foreach($countries as $c) { ?>
        <option value=<? echo html_entities($c->value); ?> > <? echo html_entities($c->text); ?></option>
    <? } ?>
</select>
```
With BladeOne, we could do the same with
```php // template.blade.php
<select>
    @foreach($countries as $c)
        <option value={{$c->value}} >{{echo html_entities($c->text)}}</option>
    @nextforeach
</select>
```
And if we use thehtml extension we could even reduce to

```php // template.blade.php
@select('id1')
    @items($countries,'value','text','','')
@endselect()
```




### Performance

This library works in two stages.   

The first is when the template is called the first time. In this case, the template is compiled and stored in a folder.   
The second time the template is called then, it uses the compiled file.   The compiled file consist mainly in native PHP, so **the performance is equals than native code.** since the compiled version IS PHP.

### Scalable

You could add and use your own function by adding a new method (or extending) to the BladeOne class. NOTE: The function should start with the name "compile"
```php
protected function compileMyFunction($expression)
{
    return $this->phpTag . "echo 'YAY MY FUNCTION IS WORKING'; ?>";
}
```

Where the function could be used in a template as follow
```php
@myFunction('param','param2'...)
```
Alternatively, BladeOne allows to run arbitrary code from any class or method if its defined.
```php
{{SomeClass::SomeMethod('param','param2'...)}}
```
## Install (pick one of the next one)

1) Download the file manually then unzip (using WinRAR,7zip or any other program) https://github.com/EFTEC/BladeOne/archive/master.zip
2) git clone https://github.com/EFTEC/BladeOne
3) Composer. See [usage](#usage)
4) wget https://github.com/EFTEC/BladeOne/archive/master.zip
   unzip master.zip

## Usage

### Without composer's autoload.php
example.php:
```php
include "lib/BladeOne.php"; // you should change it and indicates the correct route.
Use eftec\bladeone;

$views = __DIR__ . '/views'; // it uses the folder /views to read the templates
$cache = __DIR__ . '/cache'; // it uses the folder /cache to compile the result. 
$blade = new bladeone\BladeOne($views,$cache,BladeOne::MODE_AUTO);
echo $blade->run("hello",array("variable1"=>"value1")); // /views/hello.blade.php must exist
```

### Without namespace nor composer

```php
include "../lib/BladeOne.php";

// The nulls indicates the default folders. By drfault it's /views and /compiles
// \eftec\bladeone\BladeOne::MODE_DEBUG is useful because it indicates the correct file if the template fails to load.  
//  You must disable it in production. 
$blade = new \eftec\bladeone\BladeOne(null,null,\eftec\bladeone\BladeOne::MODE_DEBUG);

echo $blade->run("Test.hello", []); // the template must be in /views/Test/hello.blade.php
```

### With composer's autoload.php

```php
require "vendor/autoload.php";

Use eftec\bladeone\BladeOne;

$views = __DIR__ . '/views';
$cache = __DIR__ . '/cache';
$blade = new BladeOne($views,$cache,BladeOne::MODE_AUTO);
echo $blade->run("hello",array("variable1"=>"value1"));
```

Run the next composer command:  

> composer require eftec/bladeone


Where `$views` is the folder where the views (templates not compiled) will be stored. 
`$cache` is the folder where the compiled files will be stored.

In this example, the BladeOne opens the template **hello**. So in the views folder it should exist a file called **hello.blade.php**

views/hello.blade.php:
```html
<h1>Title</h1>
{{$variable1}}
```

## Security (optional)

```php
require "vendor/autoload.php";

Use eftec\bladeone;

$views = __DIR__ . '/views';
$cache = __DIR__ . '/cache';
$blade=new bladeone\BladeOne($views,$cache,BladeOne::MODE_AUTO);

$blade->setAuth('johndoe','admin'); // where johndoe is an user and admin is the role. The role is optional

echo $blade->run("hello",array("variable1"=>"value1"));
```

If you log in using blade then you could use the tags @auth/@endauth/@guest/@endguest


```html
@auth
    // The user is authenticated...
@endauth

@guest
    // The user is not authenticated...
@endguest
```

or

```html
@auth('admin')
    // The user is authenticated...
@endauth

@guest('admin')
    // The user is not authenticated...
@endguest
```



## Business Logic/Controller methods

### constructor
```php
$blade=new bladeone\BladeOne($views,$compile,$mode);
```
- `BladeOne(templatefolder,compiledfolder,$mode)` Creates the instance of BladeOne.
-   **$views** indicates the folder or folders (it could be an array of folders) (without ending backslash) of where the template files (*.blade.php) are located.
-   **$compile** indicates the folder where the result of files will be saved. This folder should have write permission. Also, this folder could be located outside of the Web Root.
-   **$mode** (optional).  It sets the mode of the compile. See [setMode(mode)](#setmode) .  By default it's automatic

Example:  

```php
$blade=new bladeone\BladeOne(__DIR__.'/views',__DIR__.'/compiles');
// or multiple views:
$blade=new bladeone\BladeOne([__DIR__.'/views',__DIR__.'/viewsextras'],__DIR__.'/compiles');
```


### run
```php
echo $blade->run("hello",array("variable1"=>"value1"));
```
- run([template],[array])  Runs the template and generates a compiled version (if its required), then it shows the result.
-   **template** is the template to open. The dots are used for to separate folders.  If the template is called "folder.example" then the engine tries to open the file "folder/example.blade.php"
- - If the template has a slash (/), then it uses the full literal path, ignoring the default extension.  
-   **array (optional)**. Indicates the values to use for the template.  For example ['v1'=>10'], indicates the variable $v1 is equals to 10

Examples:

```php
echo $blade->run("path.hello",array("variable1"=>"value1")); // calls the template in /(view folders)/path/hello.blade.php
echo $blade->run("path/hello.blade.php",array("variable1"=>"value1")); // calls the template in /(view folders)/path/hello.blade.php
```

### share

It adds a global variable

```php
echo $blade->share("global","valueglobal"));
echo $blade->run("hello",array("variable1"=>"value1"));
```

### setOptimize(bool=false)

If true then it optimizes the result (it removes tab and extra spaces).  
By default BladeOne will optimize the result.

```php
$blade->setOptimize(false); 
```

### setIsCompiled(bool=false)

If false then the file is not compiled and it is executed directly from the memory.
This behaviour is slow because the compiled file is used as a cache and without 
this file, then the file is compiled each time.     
By default the value is true   
It also sets the mode to MODE_SLOW   

```php
$blade->setIsCompiled(false); 
```

### setMode

It sets the mode of compilation.

> If the constant BLADEONE_MODE is defined, then it has priority over setMode()

|mode|behaviour|
|---|---|
|BladeOne::MODE_AUTO|Automatic, BladeOne checks the compiled version, if it is obsolete, then a new version is compiled and it replaces the old one|
|BladeOne::MODE_SLOW|Slow, BladeOne always compile and replace with a new version.  It is useful for development|
|BladeOne::MODE_FAST|Fast, Bladeone never compile or replace the compiled version, even if it doesn't exist|
|BladeOne::MODE_DEBUG| It's similar to MODE_SLOW but also generates a compiled file with the same name than the template.


### setFileExtension($ext), getFileExtension

It sets or gets the extension of the template file. By default, it's .blade.php

> The extension includes the leading dot.

### setCompiledExtension($ext), getCompiledExtension

It sets or gets the extension of the template file. By default, it's .bladec

> The extension includes the leading dot.




### runString
```php
echo $blade->runString('<p>{{$direccion}}</p>', array('direccion'=>'cra 20 #33-58'));
```
- runString([expression],[array])  Evaluates the expression and returns the result.
-   expression = is the expression to evaluate
-   array (optional). Indicates the values to use for the template.  For example ['v1'=>10'], indicates the variable $v1 is equals to 10

### directive
It sets a new directive (command) that runs on compile time.
```php
$blade->directive('datetime', function ($expression) {
    return "<?php echo ($expression)->format('m/d/Y H:i'); ?>";
});
```
```html
@datetime($now)
```

### directiveRT
It sets a new directive (command) that runs on runtime time.
```php
$blade->directiveRT('datetimert', function ($expression) {
    echo $expression->format('m/d/Y H:i');
});
```

```html
@datetimert($now)
```


### BLADEONE_MODE (global constant) (optional)

It defines the mode of compilation (via global constant) See [setMode(mode)](#setmode) for more information.

```php
define("BLADEONE_MODE",BladeOne::MODE_AUTO);
```

- `BLADEONE_MODE` Is a global constant that defines the behaviour of the engine.
- Optionally, you could use `$blade->setMode(BladeOne::MODE_AUTO);`

## Template tags

### Template Inheritance

### In the master page (layout)
|Tag|Note|
|---|---|
|@section('sidebar')|Start a new section|
|@show|Indicates where the content of section will be displayed|
|@yield('title')|Show here the content of a section|

### Using the master page (using the layout)
|Tag|Note|
|---|---|
|@extends('layouts.master')|Indicates the layout to use|
|@section('title', 'Page Title')|Sends a single text to a section|
|@section('sidebar')|Start a block of code to send to a section|
|@endsection|End a block of code|


Note :(*) This feature is in the original documentation but it's not implemented either is it required. Maybe it's an obsolete feature.

### variables
|Tag|Note|
|---|---|
|{{$variable1}}|show the value of the variable using htmlentities (avoid xss attacks)|
|@{{$variable1}}|show the value of the content directly (not evaluated, useful for js)|
|{!!$variable1!!}|show the value of the variable without htmlentities (no escaped)|
|{{ $name or 'Default' }}|value or default|
|{{Class::StaticFunction($variable)}}|call and show a function (the function should return a value)|

### logic
|Tag|Note|
|---|---|
|@if (boolean)|if logic-conditional|
|@elseif (boolean)|else if logic-conditional|
|@else|else logic|
|@endif|end if logic|
|@unless(boolean)|execute block of code is boolean is false|

### loop

#### @for($variable;$condition;$increment) / @endfor
_Generates a loop until the condition is meet and the variable is incremented for each loop_   

|Tag|Note|Example|
|---|---|---|
|$variable|is a variable that should be initialized.|$i=0|  
|$condition|is the condition that must be true, otherwise the cycle will end.|$i<10|
|$increment|is how the variable is incremented in each loop.|$i++|

Example:   
```html
@for ($i = 0; $i < 10; $i++)
    The current value is {{ $i }}<br>
@endfor
```
Returns:   
```html
The current value is 0
The current value is 1
The current value is 2
The current value is 3
The current value is 4
The current value is 5
The current value is 6
The current value is 7
The current value is 8
The current value is 9
```

#### @inject('variable name', 'namespace')

```html
@inject('metric', 'App\Services\MetricsService')
<div>
    Monthly Revenue: {{ $metric->monthlyRevenue() }}.
</div>
```

By default, BladeOne creates a new instance of the class `'variable name'` inside `'namespace'` with the parameterless constructor.

To override the logic used to resolve injected classes, pass a function to `setInjectResolver`.


Example with Symphony Dependency Injection.
```php
$containerBuilder = new ContainerBuilder();
$loader = new XmlFileLoader($containerBuilder, new FileLocator(__DIR__));
$loader->load('services.xml');

$blade->setInjectResolver(function ($namespace, $variableName) use ($loader) {
    return $loader->get($namespace);
});
```



#### @foreach($array as $alias) / @endforeach
Generates a loop for each values of the variable.    

|Tag|Note|Example|
|---|---|---|
|$array|Is an array with values.|$countries|  
|$alias|is a new variable that it stores each interaction of the cycle.|$country|

Example: ($users is an array of objects)
```html
@foreach($users as $user)
    This is user {{ $user->id }}
@endforeach
```
Returns:
```html
This is user 1
This is user 2
```

#### @forelse($array as $alias) / @empty / @endforelse
Its the same as foreach but jumps to the `@empty` tag if the array is null or empty   

|Tag|Note|Example|
|---|---|---|
|$array|Is an array with values.|$countries|  
|$alias|is a new variable that it stores each interaction of the cycle.|$country|


Example: ($users is an array of objects)
```html
@forelse($users as $user)
    <li>{{ $user->name }}</li>
@empty
    <p>No users</p>
@endforelse
```
Returns:
```html
John Doe
Anna Smith
```

#### @while($condition) / @endwhile
Loops until the condition is not meet.

|Tag|Note|Example|
|---|---|---|
|$condition|The cycle loops until the condition is false.|$counter<10|  


Example: ($users is an array of objects)
```html
@set($whilecounter=0)
@while($whilecounter<3)
    @set($whilecounter)
    I'm looping forever.<br>
@endwhile
```
Returns:
```html
I'm looping forever.
I'm looping forever.
I'm looping forever.
```

#### @splitforeach($nElem,$textbetween,$textend="")  inside @foreach
This functions show a text inside a `@foreach` cycle every "n" of elements.  This function could be used when you want to add columns to a list of elements.   
NOTE: The `$textbetween` is not displayed if its the last element of the last.  With the last element, it shows the variable `$textend`

|Tag|Note|Example|
|---|---|---|
|$nElem|Number of elements|2, for every 2 element the text is displayed|  
|$textbetween|Text to show|`</tr><tr>`| 
|$textend|Text to show|`</tr>`| 

Example: ($users is an array of objects)
```html
<table border="1">
<tr>
@foreach($drinks7 as $drink)
    <td>{{$drink}}</td>
    @splitforeach(2,'</tr><tr>','</tr>')
    @endforeach
</table>
```
Returns a table with 2 columns.

#### @continue / @break
Continue jump to the next iteration of a cycle.  `@break` jump out of a cycle.

|Tag|Note|Example|
|---|---|---|

Example: ($users is an array of objects)
```html
@foreach($users as $user)
    @if($user->type == 1) // ignores the first user John Smith
    @continue
    @endif
    <li>{{ $user->type }} - {{ $user->name }}</li>

    @if($user->number == 5) // ends the cycle.
        @break
    @endif
@endforeach
```
Returns:
```html
2 - Anna Smith
```
### switch / case

_Example:(the indentation is not required)_
```html
@switch($countrySelected)
    @case(1)
        first country selected<br>
    @break
    @case(2)
        second country selected<br>
    @break
    @defaultcase()
        other country selected<br>
@endswitch()
```

- `@switch` The first value is the variable to evaluate.
- `@case` Indicates the value to compare.  It should be run inside a @switch/@endswitch
- `@default` (optional) If not case is the correct then the block of @defaultcase is evaluated.
- `@break` Break the case
- `@endswitch` End the switch.

### Sub Views
|Tag|Note|
|---|---|
|@include('folder.template')|Include a template|
|@include('folder.template',['some' => 'data'])|Include a template with new variables|
|@each('view.name', $array, 'variable')|Includes a template for each element of the array|
Note: Templates called folder.template is equals to folder/template

## @include
It includes a template

You could include a template as follow:
```html
<div>
    @include('shared.errors')
    <form>
        <!-- Form Contents -->
    </form>
</div>
```

You could also pass parameters to the template
```html
@include('view.name', ['some' => 'data'])
```
### @includeif

Additionally, if the template doesn't exist then it will fail. You could avoid it by using includeif
```html
@includeIf('view.name', ['some' => 'data'])
```
### @includefast

`@Includefast` is similar to `@include`. However, it doesn't allow parameters because it merges the template in a big file (instead of relying on different files), so it must be fast at runtime by using more space on the hard disk versus less call to read a file.


```html
@includefast('view.name')
```

>This template runs at compile time, so it doesn't work with runtime features such as @if() @includefast() @endif()

### aliasing include

Laravel's blade allows to create aliasing include. Laravel calls this method "include()". However, PHP 5.x doesn't allow to use
the name "include()" so in this library is called "**addInclude()**". 

How it work?

If your BladeOne includes are stored in a sub-directory, you may wish to alias them for easier access. For example, imagine a BladeOne include that is stored at views/includes/input.blade.php with the following content:

📁 views/includes/input.blade.php

    <input type="{{ $type ?? 'text' }}">

You may use the include method to alias the include from includes.input to input. 

    Blade->addInclude('includes.input', 'input');

Once the include has been aliased, you may render it using the alias name as the Blade directive:

    @input(['type' => 'email'])



## Comments
|Tag|Note|
|---|---|
|{{-- text --}}|Include a comment|

### Stacks
|Tag|Note|
|---|---|
|@push('elem')|Add the next block to the push stack|
|@pushonce('elem')|Add the next block to the push stack. It is only pushed once.|
|@endpush|End the push block|
|@stack('elem')|Show the stack|

```html
@push('scripts')
script1
@endpush
@push('scripts')
script2
@endpush
@push('scripts')
script3
@endpush
<hr>
@stack('scripts')
<hr>
```

It returns 

```html
<hr>
script1 script2 script3
<hr>
```

```html
@pushonce('scripts')
script1
@endpushonce
@pushonce('scripts')
script2
@endpushonce
@pushonce('scripts')
script3
@endpushonce
<hr>
@stack('scripts')
<hr>
```

It returns 

```html
<hr>
script1
<hr>
```



## @set (new for 1.5)
```
@set($variable=[value])
```
`@set($variable)` is equals to `@set($variable=$variable+1)`
- `$variable` defines the variable to add. If not value is defined and it adds +1 to a variable.
- value (option) define the value to use.

### Service Inject
|Tag|Note|
|---|---|
|@inject('metrics', 'App\Services\MetricsService')|Used for insert a Laravel Service|NOT SUPPORTED|

## Asset Management

The next libraries are designed to work with assets (CSS, JavaScript, images and so on). While it's possible to show an asset without a special library but it's a challenge if you want to work with relative path using an MVC route.

For example, let's say the next example:
http://localhost/img/resource.jpg

you could use the full path.
```html
<img src='http://localhost/img/resource.jpg' />
```
However, it will fail if the server changes.
So, you could use a relative path.
```html
<img src='img/resource.jpg' />
```
However, it fails if you are calling the web
http://localhost/controller/action/

because the browser will try to find the image at
http://localhost/controller/action/img/resource.jpg
instead of
http://localhost/img/resource.jpg

So, the solution is to set a base URL and to use an absolute or relative path

Absolute using `@asset`
```html
<img src='@asset("img/resource.jpg")' />
```
is converted to
```html
<img src='http://localhost/img/resource.jpg' />
```

Relative using @relative
```html
<img src='@relative("img/resource.jpg")' />
```
is converted to (it depends on the current url)
```html
<img src='../../img/resource.jpg' />
```

It is even possible to add an alias to resources. It is useful for switching from local to CDN.

```php
$blade->addAssetDict('js/jquery.min.js','https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js');
```
so then
```html
@asset('js/jquery.min.js')
```

returns
```html
https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js
```

:file_folder: Example: [BladeOne/examples/relative1/relative2/callrelative.php](https://github.com/EFTEC/BladeOne/blob/master/examples/examplerelative.php)


### @asset
It returns an absolute path of the resource. 

```html
@asset('js/jquery.js')
```
Note: it requires to set the base address as 
```php
$obj=new BladeOne();
$obj->setBaseUrl("https://www.example.com/urlbase/"); // with or without trail slash
```
> Security: Don't use the variables $SERVER['HTTP_HOST'] or $SERVER['SERVER_NAME'] unless the url is protected or the address is sanitized.

### @resource

It's similar to `@asset`. However, it uses a relative path.
```
@resource('js/jquery.js')
```


Note: it requires to set the base address as 
```php
$obj=new BladeOne();
$obj->setBaseUrl("https://www.example.com/urlbase/"); // with or without trail slash
```

### setBaseUrl($url)
It sets the base url.

```php
$obj=new BladeOne();
$obj->setBaseUrl("https://www.example.com/urlbase/"); // with or without trail slash
```


### getBaseUrl()
It gets the current base url.

```php
$obj=new BladeOne();
$url=$obj->getBaseUrl(); 
```

### addAssetDict($name,$url)
It adds an alias to an asset. It is used for `@asset` and `@relative`. If the name exists then `$url` is used.

```php
$obj=new BladeOne();
$url=$obj->addAssetDict('css/style.css','http://....'); 
```


## Extensions Libraries (optional)
[BladeOneHtml Documentation](BladeOneHtml.md)

[BladeOneCache Documentation](BladeOneCache.md)

[BladeOneLang Documentation](BladeOneLang.md)

## Definition of Blade Template
https://laravel.com/docs/5.6/blade

## Differences between Blade and BladeOne

- Laravel's extension removed.
- Dependencies to other class removed (around 30 classes).
- The engine is self-contained.
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

This new syntax adds an (optionally) non-selected row.
Also, BladeOneHTML adds multiple select, fixed values (without array), grouped select and many more.



## Version

- 2019-05-25 3.27 #78 Some comments are corrected. The views folder(s) could be an string or an array.  It also allows to specific the route of the template literally. Now  "folder.template" is equals to "folder/template.blade.php"
- 2019-05-25 3.26 #75 added method @pushonce('namestack') and @endpuchonce
- 2019-05-24 3.25 #77 added method setOptimize(bool) and setIsCompiled(bool)
- 2019-05-05 3.24 #75
- 2019-04-24 10k downloads 👏 👏 👏!  
- 2019-04-23 3.23 method share()
- 2019-04-03 3.22 it solves #70. It also adds some small optimizations.
- 2019-03-07 3.21 method "addInclude()" for aliasing include.  
- 2019-03-01 3.20 I checked Laravel's blade and there is nothing new. This version is aligned with Blade 5.8
-  I added some fixes to @json. Now it allows arguments (the same arguments than PHP's json_encode)
- 2019-01-18 3.19 New changes on new pull from @AVONnadozie 
- 2018-12-16 3.18 Maintenance version. I checked and BladeOne already support the new features of 5.7 (@method and @csrf)  
- 2018-10-25 3.17 Halloween version.  Now if the command doesn´t exist,for example @media @font-face and such, 
 it doesn't show the error but returns the same text as verbatim.  
- 2018-10-25 3.16 Fixed an error with compile() when it's called with information.   
- - Now compile() returns true or false
- - isExpected() has an optional argument. 
- - getCompiledFile() and getTemplateFile() now uses the default template ($this->fileName) if the arg is empty (null or '')
- - arguments $fileName are now called $templateName because $filename is not a filename (temp/file.blade.php) but the name of the template (temp.file)
- 2018-10-22 3.15 Fixed an error if _e() is called by an array or object.
- 2018-10-09 3.14 Added @includefast
- 2018-10-06 3.13 Added @relative, setBaseUrl(),getBaseUrl() and addAssetDict().  @asset is changed, now it allows dictionary. $baseUrl is not public anymore
- 2018-09-29 3.12 Added the function setPath so we can change the path of the templates/compile files at runtime.
- 2018-09-21 3.11 @includeif fixed.
- 2018-09-21 3.10 Testing travis.
- 2018-09-16 3.9 Added unit test (gamification) and travis :rocket:
- 2018-09-01 3.8 Sorry Blade class,but you must go.  \eftec\bladeone\BladeOneBlade.php is no-more so the static call and the instance. (#47)[https://github.com/EFTEC/BladeOne/issues/47] it was the last straw.
- 2018-08-29 3.7 phpdoc block reduced. "To do" comments deleted.  Fixed issue (#44)[https://github.com/EFTEC/BladeOne/issues/44]
- 2018-08-24 3.5 Some fixes.
- 2018-08-16 3.4 Custom if,@php tag and some fixes with @switch
- 2018-08-08 3.3 Set extensions, constants and blade mode.
- 2018-08-05 3.2 Fixed composer's problem
- 2018-07-27 3.1 custom directive and directivert(runtime).
- 2018-07-12 3.0 BladeOneLogic now is fused with BladeOne. And a lot of new changes.
- 2018-07-11 2.4 Some fixes, new tags @json(var),@isset($records),@endisset,@includewhen,@includefirst,@prepend,@endprepend,@empty,@endempty,@append
- 2018-06-12 2.3.3 Reorder folders.
- 2018-06-11 2.3.2 Fixed bladeonehtml to allows readonly value.
- 2018-05-06 2.3.1 Fixed a problem with @verbatim. Add the method runString for evaluating a function
- 2018-04-13 2.3 The end result clean spaces.   PHPDoc cleaned (checked with PHPStorm 2018.1). Fixed some typos (sorry about that).     
- 2017-12-18 Added BladeOneLang
- 2017-12-14 2.2 Added @asset
- 2017-10-20 2.1 Fixed with @parent
    Fixed @item (BladeOneHtml). Now, it considers null and 0 as different.
    Fixed foreach bug when the name of the variable contains the letters 'as' for example @foreach($list**As**Fast as $v)      
- 2017-09-28 2.0 Some fixes there and here.  
- 2017-07-21 1.9 Components and Slots.  Note: I'm not really convinced in its usability.
- 2017-05-24 1.8 Maintenance.  Now, it runs with or without mb_string module
- 2017-04-09 1.8 Creates a directory automatically. Some fixes. Add new feature **@splitforeach**.    
- 2017-02-20 1.6 More cleanups. Refactored file, image, and other tags.
- 2016-08-14 1.6 Some cleanups. Add new documentation   
- 2016-07-03 1.5 New features such as **@set** command
- 2016-06-24 1.4 Updates extensions.  Now it uses strut instead of classes. Added a new extension BladeOneCache.
- 2016-06-12 1.3 Lots of clean up. I removed some unused parameters.  I fixed a problem with forced in BladeOne.  I separate the doc per extension.
- 2016-06-10 1.2 New changes.  Added namespaces (for autocomplete and compatibility with Composer)
- 2016-06-09 1.1 Some fine tune.
- 2016-06-09 1.0 Version. Most works. Added extensions and error control with a tag are not defined.
- 2016-06-08 0.2. Beta First publish launch.


### Changes between 2.x and 3.0

- @defaultcase now is called @default  (BladeOneLogic)
- @break is now required for @switch/@case
- BladeOneLogic is now merged with BladeOne.  BladeOneLogic is discontinued.
- New tags of security (optional).
- New tags for injection

## todo

- @section / @show versus @endsection

- hello@@world fails to render hello@world.  However, hello @@world render hello@world.  

- extends bug. If you use extends then, every content after the last @endsetion will be rendered at the top of the page.  
  Solution: avoid to add any content after the last @endsection, including spaces and empty lines.

- Some functionalities are not available for PHP lower than 7.0.

bad example:
```html
@extends("_shared.htmltemplate")
@section("content")
@endsection
this is a bug
```

result:
```html
this is a bug
<!DOCTYPE html>
<html>
   <head>....</head>
   <body>....</body>
</html>
```

bad too: (check the empty line at the bottom).  This is not as bad but a small annoyance.
```html
@endsection(line carriage)
(empty line)
```

good:
```html
@endsection
```
## SourceGuardian

This library is compatible with [SourceGuardian](https://www.sourceguardian.com).   
 
>SourceGuardian provides full PHP 4, PHP 5 and PHP 7 support including the latest PHP 7.2 along with many other protection and encryption features.
 
However:  
 
* You must avoid encoding the template folder (copy unencoded the views folder).
* Optionally, you must avoid encoding the compiled folder because the files could be replaced by Bladeone. Also, you could run BladeOne in mode `BladeOne::MODE_FAST` and encode the compile folder)      

So,   
* **\view** folder = copy unencoded.
* **\compiled** folder (BladeOne::MODE_FAST)= php/html script (encode)
* **\compiled folder** (anything but BladeOne::MODE_FAST)= skip files (because it will be replaced)
* **(everything else)** = php/html script (encode)

I don't know about the compatibility of [Ioncube](http://www.ioncube.com/) or [Zend Guard](http://www.zend.com/en/products/zend-guard) I don't own a license of it.  


## Collaboration

You are welcome to use it, share it, ask for changes and whatever you want to. Just keeps the copyright notice in the file.

## Future
* Blade locator/container

## Missing

Some features are missing because they are new, or they lack documentation or they are specific to Laravel (then, they are useless without it)

- Laravel's own commands. Reason: This library is free of Laravel
- ~~Custom if. Reason: It is dangerous and odds.~~ DONE
- blade extension Reason: Extensions (that is part of the code, not in the template) is managed differently on BladeOne.
- ~~@php. Pending. I'm not so sure to implement this one. If you are using this one, then you are doing it wrong.~~ DONE
- ~~@canany. Pending. :baby_chick:~~ DONE
- ~~@can ( https://laravel.com/docs/5.6/authorization ). Pending~~ DONE
- ~~@cannot. Pending~~ DONE
- ~~@elseauth. Pending~~ DONE
- ~~@elseguest. Pending~~ DONE
- ~~@dump. Done. Ugly but it is done~~ DONE
- ~~@elsecan. Pending~~ DONE
- ~~@elsecanany. Pending :baby_chick:~~ DONE
- ~~@elsecannot. Pending~~ DONE
- ~~@endcanany. Pending :baby_chick:~~ DONE
- ~~@endcannot. Pending~~ DONE
- ~~@endunless. Pending~~ DONE
- ~~@csrf. Pending~~ DONE
- ~~@dd. Done. Ugly but it is done too.~~ DONE
- ~~@method. Pending~~ DONE







## License
MIT License.
BladeOne (c) 2016-2019 Jorge Patricio Castro Castillo
Blade (c) 2012 Laravel Team (This code is based and inspired in the work of the team of Laravel, however BladeOne is mostly a original work)

