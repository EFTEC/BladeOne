<!DOCTYPE html>
<html>
<body>
<h1>Cache test</h1>
<h2>Test the cache system. Two part of the code is cached for 5 seconds</h2>
<h2>Additionally, in testcache.php, the code avoids to re-read a variable if the cache is still active</h2>
@cache("1",5)
    <hr>
    start of the cache n1:<br>
    this information should be cached {{$random}} {{$time}} unless we are past  {{$timeUpTo}}<br>
    @foreach($list as $item)
        {{$item}}<br>
    @endforeach
    end of the cache<br>
    <hr>
@endcache()
    this information should not be cached {{$random}} {{$time}} <br>
@cache("2",5)
    <hr>
    start of the cache N2:<br>
    this information should also be cached {{$random}} {{$time}} unless we are past  {{$timeUpTo}}<br>
    end of the cache<br>
    <hr>
@endcache()


</body>
</html>
