<!-- Stored in resources/views/layouts/app.blade.php -->

<html>
<head>
    <title>App Name - @yield('title')</title>
</head>
<body>
@section('sidebar')
*********This is the master sidebar.(from layout) *************<br>
@show

<div class="container">
    @yield('content')
</div>
</body>
</html>