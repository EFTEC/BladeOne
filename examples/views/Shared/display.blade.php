<div style="background-color: lightblue">
    <br>
    Global variable :{{$g1}} {{$g2}}<br>
    Scope variable :{{$s1 | "s1 is not defined"}} {{$s2 | "s2 is not defined"}}<br>
    @@include('shared.subdisplay',['s2'=>'defined inside display'])<br>
    @include('shared.subdisplay',['s2'=>'defined inside display'])<br>
    Global variable :{{$g1}} {{$g2}}<br>
    Scope variable :{{$s1 | "s1 is not defined"}} {{$s2 | "s2 is not defined"}}<br>


    <br>
</div>