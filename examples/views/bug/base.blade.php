<!DOCTYPE html>
<html lang="lang">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

     wp_head() 

    @asset('app.css')

    @yield('head')

</head>

<body>

@@include('layout/header.blade.php')

<main>
    <hr>
    @yield('content', $this->runChild("bug/content.blade.php"))
    <hr>
</main>

@@include('layout/sidebar.blade.php')<br>

@@include('layout/footer.blade.php')<br>

wp_footer() <br>

@asset('app.js')<br>

</body>

</html>