<h1>@yield('title')</h1>
<hr>
@section('content')
    @show
<hr>
<ul>
@foreach($countries as $country)
    <li>{{$country}}</li>
@endforeach
</ul>    

