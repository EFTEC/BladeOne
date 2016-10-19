#Templates
##Template Inheritance

Example  

_master.blade.php_ is the layout/masterpage template :
```html
<h1>Title</h1>
@section('header')
@show
....
@yield('footer')
```
_page.blade.php_ is the template that is using the layout page:
```html
@extends('master')
@section('header')
<head>....</head>
@endsection

```


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
