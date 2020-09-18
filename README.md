![Logo](https://raw.githubusercontent.com/EFTEC/BladeOne/gh-pages/images/bladelogo.png)

# BladeOne Blade Template Engine
BladeOne is a standalone version of Blade Template Engine that uses a single PHP file and can be ported and used in different projects. It allows you to use blade template outside Laravel.

Бладеоне-это отдельная версия ядра Blade-шаблонов, которая использует один PHP-файл и может быть портирована и использована в различных проектах. Он позволяет использовать шаблон Blade за пределами laravel.    

[![Build Status](https://travis-ci.org/EFTEC/BladeOne.svg?branch=master)](https://travis-ci.org/EFTEC/BladeOne)
[![Packagist](https://img.shields.io/packagist/v/eftec/bladeone.svg)](https://packagist.org/packages/eftec/bladeone)
[![Total Downloads](https://poser.pugx.org/eftec/bladeone/downloads)](https://packagist.org/packages/eftec/bladeone)
[![Maintenance](https://img.shields.io/maintenance/yes/2020.svg)]()
[![composer](https://img.shields.io/badge/composer-%3E1.6-blue.svg)]()
[![php](https://img.shields.io/badge/php->5.6-green.svg)]()
[![php](https://img.shields.io/badge/php-7.x-green.svg)]()
[![CocoaPods](https://img.shields.io/badge/docs-70%25-yellow.svg)]()


NOTE: So far it's apparently the only one project that it's updated with the latest version of **Blade 7 (March 2020)**. It misses some commands [missing](#missing) but nothing more.


Примечание: до сих пор это, видимо, только один проект, который обновляется с последней версией ** Blade 7 (2020 Марта) **. Он пропускает некоторые команды [отсутствует](#missing), но ничего больше.

## Comparison with Twig

> (spoiler) Twig is slower. 😊         

|          | First Time Time | First Time Memory | Overload First Time | Second Time | Second Time Memory |
|----------|-----------------|-------------------|---------------------|-------------|--------------------|
| BladeOne | 1962ms          | 2024kb            | 263                 | 1917ms      | 2024kb             |
| Twig     | 3734ms          | 2564kb            | 123                 | 3604ms      | 2327kb             |

What it was tested?.  It was tested two features (that are the most used):   It was tested with an array with 
1000 elements and tested many times.

[Comparison with Twig](https://github.com/EFTEC/BladeOne/wiki/Comparison-with-Twig)



## NOTE about questions, reports, doubts or suggesting:

✔ If you want to open an inquiry, do you have a doubt, or you find a bug, then you could open an [ISSUE](https://github.com/EFTEC/BladeOne/issues).   
Please, don't email me (or send me PM) directly for question or reports.    
Also, if you want to reopen a report, then you are open to do that.     
I will try to answer all and every one of the question (in my limited time).    

## Some example
| [ExampleTicketPHP](https://github.com/jorgecc/ExampleTicketPHP) | [Example cupcakes](https://github.com/EFTEC/example.cupcakes) | [Example Search](https://github.com/EFTEC/example-search)    | [Example Editable Grid](https://github.com/EFTEC/example-php-editablegrid) |
| ------------------------------------------------------------ | ------------------------------------------------------------ | ------------------------------------------------------------ | ------------------------------------------------------------ |
| <img src="https://camo.githubusercontent.com/3c938f71f46a90eb85bb104f0f396fcba62b8f4a/68747470733a2f2f74686570726163746963616c6465762e73332e616d617a6f6e6177732e636f6d2f692f3436696b7061376661717677726533797537706a2e6a7067" alt="example php bladeone" width="200"/> | <img src="https://github.com/EFTEC/example.cupcakes/raw/master/docs/result.jpg" alt="example php bladeone cupcakes" width="200"/> | <img src="https://github.com/EFTEC/example-search/raw/master/img/search_bootstrap.jpg" alt="example php bladeone search" width="200"/> | <img src="https://github.com/EFTEC/example-php-editablegrid/raw/master/docs/final.jpg" alt="example php bladeone search" width="200"/> |

[https://www.southprojects.com](https://www.southprojects.com)


## Manual

* [BladeOne Manual](https://github.com/EFTEC/BladeOne/wiki/BladeOne-Manual)    
* [Template tags (views)](https://github.com/EFTEC/BladeOne/wiki/Template-tags)    
    * [Template variables](https://github.com/EFTEC/BladeOne/wiki/Template-variables)     
    * [Template inheritance](https://github.com/EFTEC/BladeOne/wiki/Template-inheritance)  
    * [Template component](https://github.com/EFTEC/BladeOne/wiki/Template-Component)            
    * [Template stack](https://github.com/EFTEC/BladeOne/wiki/Template-stack)
    * [Template asset, relative, base, current and canonical links](https://github.com/EFTEC/BladeOne/wiki/Template-Asset,-Relative,-Base-and-Canonical-Links)
    * [Template calling methods](https://github.com/EFTEC/BladeOne/wiki/Template-calling-methods) 
    * [Template logic](https://github.com/EFTEC/BladeOne/wiki/Template-logic)    
    * [Template loop](https://github.com/EFTEC/BladeOne/wiki/Template-loop)    
    * [Template Pipes (Filter)](https://github.com/EFTEC/BladeOne/wiki/Template-Pipes-(Filter))    
* [Methods of the class](https://github.com/EFTEC/BladeOne/wiki/Methods-of-the-class)   
* [Injecting logic before the view (composer)](https://github.com/EFTEC/BladeOne/wiki/Injecting-logic-before-the-view-(composer))
* [Extending the class](https://github.com/EFTEC/BladeOne/wiki/Extending-the-class)   
* [Using BladeOne with YAF Yet Another Framework](https://github.com/EFTEC/BladeOne/wiki/Using--BladeOne-with-YAF)
* [Differences between Blade and BladeOne](https://github.com/EFTEC/BladeOne/wiki/Differences-between-Blade-and-BladeOne)   
* [Comparision with Twig (May-2020)](https://github.com/EFTEC/BladeOne/wiki/Comparison-with-Twig)
* [Changelog](https://github.com/EFTEC/BladeOne/wiki/Changelog)   
* [Changes between 2.x and 3.0 and TODO](https://github.com/EFTEC/BladeOne/wiki/Changes-between-2.x-and-3.0-and-TODO)   
* [Code Protection (Sourceguardian and similars)](https://github.com/EFTEC/BladeOne/wiki/Code-Protection-(Sourceguardian-and-similars))   




## Laravel blade tutorial

You can find some tutorials and example on the folder [Examples](examples).

You could also check the wiki [Wiki](https://github.com/EFTEC/BladeOne/wiki)

## About this version
By standard, The original Blade library is part of Laravel (Illuminate components) and to use this template library, you require install Laravel and Illuminate-view components.
The syntax of Blade is pretty nice and bright. It's based in C# Razor (another template library for C#). It's starting to be considered a de-facto standard template system for many PHP (Smarty has been riding off the sunset since years ago) so, if we can use it without Laravel then its a big plus for many projects. 
In fact, in theory, it is even possible to use with Laravel.
Exists different versions of Blade Template that runs without Laravel, but most requires 50 or more files, and those templates add a new level of complexity, so they are not removing Laravel but hiding:

- More files to manage.
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

The first is when the template calls the first time. In this case, the template compiles and store in a folder.   
The second time the template calls then, it uses the compiled file.   The compiled file consist mainly in native PHP, so **the performance is equals than native code.** since the compiled version IS PHP.

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

If you use **composer**, then you could add the library using the next command (command line)  

> composer require eftec/bladeone

If you don't use it, then you could download the library and include it manually.

### Implicit definition

```php
use eftec\bladeone\BladeOne;

$views = __DIR__ . '/views';
$cache = __DIR__ . '/cache';
$blade = new BladeOne($views,$cache,BladeOne::MODE_DEBUG); // MODE_DEBUG allows to pinpoint troubles.
echo $blade->run("hello",array("variable1"=>"value1")); // it calls /views/hello.blade.php
```

Where `$views` is the folder where the views (templates not compiled) will be stored. 
`$cache` is the folder where the compiled files will be stored.

In this example, the BladeOne opens the template **hello**. So in the views folder it should exist a file called **hello.blade.php**

views/hello.blade.php:
```html
<h1>Title</h1>
{{$variable1}}
```

### Explicit

In this mode, it uses the folders ```__DIR__/views``` and ```__DIR__/compiles```, also it uses the mode as MODE_AUTO.

```php
use eftec\bladeone\BladeOne;

$blade = new BladeOne(); // MODE_DEBUG allows to pinpoint troubles.
echo $blade->run("hello",array("variable1"=>"value1")); // it calls /views/hello.blade.php
```

### Fluent

```php
use eftec\bladeone\BladeOne;

$blade = new BladeOne(); // MODE_DEBUG allows to pinpoint troubles.
echo $blade->setView('hello')    // it sets the view to render
           ->share(array("variable1"=>"value1")) // it sets the variables to sends to the view            
           ->run(); // it calls /views/hello.blade.php
```

## Filter (Pipes)

It is possible to modify the result by adding filters to the result.

Let's say we have the next value $name='Jack Sparrow'

```php
$blade=new BladeOne();
$blade->pipeEnable=true; // pipes are disable by default so it must be enable.
echo $blade->run('template',['name'=>'Jack Sparrow']);
```

Our view could look like:

```php
 {{$name}}  or {!! $name !!} // Jack Sparrow
```

What if we want to show the name in uppercase?.

We could do in our code $name=strtoupper('Jack Sparrow'). With Pipes, we could do the same as follow:

```php
 {{$name | strtoupper}} // JACK SPARROW 
```

We could also add arguments and chain methods.

```php
 {{$name | strtoupper | substr:0,5}} // JACK
```

You can find more information on https://github.com/EFTEC/BladeOne/wiki/Template-Pipes-(Filter)



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



## Extensions Libraries (optional)

[BladeOneCache Documentation](BladeOneCache.md)

[https://github.com/eftec/BladeOneHtml](https://github.com/eftec/BladeOneHtml)


## Calling a static methods inside the template.

Since **3.34**, BladeOne allows to call a static method inside a class.

Let's say we have a class with namespace \namespace1\namespace2

```php
namespace namespace1\namespace2 {
    class SomeClass {
        public static function Method($arg='') {
            return "hi world";
        }
    }
}
```

### Method 1 PHP Style

We could add a "use" in the template.  Example:

Add the next line to the template
```html
@use(\namespace1\namespace2)
```

and the next lines to the template (different methods)

```html
{{SomeClass::Method()}}
{!! SomeClass::Method() !!}
@SomeClass::Method()
```

> All those methods are executed at runtime


### Method 2 Alias
Or we could define alias for each classes.

php code:
```php
    $blade = new BladeOne();
    // with the method addAliasClasses
    $blade->addAliasClasses('SomeClass', '\namespace1\namespace2\SomeClass');
    // with the setter setAliasClasses
    $blade->setAliasClasses(['SomeClass'=>'\namespace1\namespace2\SomeClass']);
    // or directly in the field
    $blade->aliasClasses=['SomeClass'=>'\namespace1\namespace2\SomeClass'];
```

Template:
```html
{{SomeClass::Method()}}
{!! SomeClass::Method() !!}
@SomeClass::Method()
```

> We won't need alias or use for global classes.



## Named argument (since 3.38)

BladeOne allows named arguments.  This feature must be implemented per function.

Let's say the next problem:

It is the old library BladeOneHtml:

```
@select('id1')
    @item('0','--Select a country--',"",class='form-control'")
    @items($countries,'id','name',"",$countrySelected)
@endselect
```

And it is the next library:

```html
@select(id="aaa" value=$selection values=$countries alias=$country)
    @item(value='aaa' text='-- select a country--')
    @items( id="chkx" value=$country->id text=$country->name)
@endselect
```

The old method **select** only allows a limited number of arguments. And the order of the arguments is important.

The new method **select** allows to add different types of arguments 

## BladeOneHtml

It is a new extension to BladeOne. It allows to create html components easily and with near-to-native performance.

It uses a new feature of BladeOne: named arguments

Example to create a select:

```html
@select(id="aaa" value=$selection values=$countries alias=$country)
    @item(value='aaa' text='-- select a country--')
    @items( id="chkx" value=$country->id text=$country->name)
@endselect
```

[https://github.com/eftec/BladeOneHtml](https://github.com/eftec/BladeOneHtml)

You could download it or add it via Composer

> composer require eftec/bladeonehtml


## Collaboration

You are welcome to use it, share it, ask for changes and whatever you want to. Just keeps the copyright notice in the file.

## Future
* Blade locator/container



## License
MIT License.
BladeOne (c) 2016-2020 Jorge Patricio Castro Castillo
Blade (c) 2012 Laravel Team (This code is based and inspired in the work of the team of Laravel, however BladeOne is mostly a original work)

