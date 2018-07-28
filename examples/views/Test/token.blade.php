<h1>Testing the token {{$token}}</h1>
<form method="post">
    @csrf
    <input type="text" name="field" value="{{$field}}" /><br/>
    <input type="submit" name="button" value="send"/>

    @if($isValid)
        <hr>Token is valid<hr>
    @else
        <hr><span style="background-color:red">Token ERROR</span><hr>
    @endif()

</form>


</form>