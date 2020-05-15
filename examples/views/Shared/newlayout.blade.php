<html>
<head>
</head>
<body>
<h1>Test</h1>
This example shows the use of @@extends, and @@yield<br><br>
    @yield('header','<br>header content is missing<br>')
    @yield('content')
    @yield('footer')
    
    @yield('footermissing',$this->runChild("Test.footermising"))
    

</body>
</html>