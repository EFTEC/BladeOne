<html>
<head>
    <title>App Name - @yield('title')</title>
    <!-- Google Fonts -->
    <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Roboto:300,300italic,700,700italic">
    <!-- CSS Reset -->
    <link rel="stylesheet" href="//cdn.rawgit.com/necolas/normalize.css/master/normalize.css">
    <!-- Milligram CSS minified -->
    <link rel="stylesheet" href="//cdn.rawgit.com/milligram/milligram/master/dist/milligram.min.css">
</head>
<body>
<h1>Example</h1>
<em>This file is Shared/newlayout.blade.php</em><br>
@section('sidebar')
    IT IS NOT VISIBLE.  A BUG???
@show
<hr>

<div class="container">
    <hr>container<br>
    @yield('content')
    <hr>
</div>
</body>
</html>