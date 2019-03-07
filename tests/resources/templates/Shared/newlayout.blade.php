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
    <b style="background-color: cadetblue"> its the parent of the siderbar</b>
@show
<hr>
@section('sidebar2')
    <b style="background-color: cadetblue"> its the parent of the siderbar2</b>
@show
<hr>


<div class="container">
    <hr style="background-color: cadetblue">container<br>
    @yield('footer')
    <hr>
</div>
</body>
</html>