<!DOCTYPE html>
<html lang="en">
<body>
<h1>Cache test</h1>
<h2>Test the cache system. Two part of the code is cached for 5 seconds</h2>
<h2>Additionally, in testcache.php, the code avoids to re-read a variable if the cache is still active</h2>
    <hr>
    start of the cache n1:<br>
    this information should be cached {{$random}} {{$time}} unless we are past  {{$timeUpTo}}<br>
    @foreach($list as $item)
        {{$item}}<br>
    @endforeach
<hr>$_GET:<br>
@dump($_GET)
<hr>
<a href="?id=2">go to ?id=2</a><br>
<a href="?id=3">go to ?id=3</a><br>
<a href="?id=3&p=3">go to ?id=3&p=3</a><br>

</body>
</html>
