<html>
<head>
    <title>App Name - @yield('title')</title>
</head>
<body>
<hr>body<br>
@section('sidebar')
    This is the master sidebar (this content is in the Shared layout)
@show
<hr>

<div class="container">
    <hr>container<br>
    @yield('content')
    <hr>
</div>
</body>
</html>