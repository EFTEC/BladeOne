baseurl: <b>{!! $baseurl !!}</b><br>
url: <b>{!! $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] !!}</b><br>
<hr>
relative url: <b>@@relative('img/cleaning.gif')</b><br>
relative url: <b>@relative('img/cleaning.gif')</b><br>
<img src="@relative('img/cleaning.gif')"><br>
<hr>
asset url: <b>@@asset('img/cleaning.gif')</b><br>
asset url: <b>@asset('img/cleaning.gif')</b><br>

<img src="@asset('img/cleaning.gif')">
<hr>
using asset dictionary <span id="loaded" style="color:red">...</span> <br>
asset url: <b>@@asset('js/jquery.min.js')</b><br>
asset url: <b>@asset('js/jquery.min.js')</b><br>

<script src="@asset('js/jquery.min.js')"></script>
<script>
    window.onload = function() {
        if (window.jQuery) {
            // jQuery is loaded
            $('#loaded').html('JQuery is loaded');

        } else {
            // jQuery is not loaded
            $('#loaded').html('JQuery failed to load');
        }
    }

</script>