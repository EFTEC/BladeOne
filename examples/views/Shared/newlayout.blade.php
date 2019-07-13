<html>
<head>
</head>
<body>
<h1>Test</h1>

    @@yield('footer')
    @yield('footer')
    <hr>
    @@yield('footermissing')
    @yield('footermissing',$this->runChild("Test.footermising"))
    <hr>

</body>
</html>