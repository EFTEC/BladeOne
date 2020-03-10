
@itsnotanoperation

@itsnotanoperation(aaa,bbb)

@include('Test.master', ['some' => 'data'])

v2<br>

Commentary {{-- this shouldn't appear --}}<br>

Hello World {{$name}}<br>

Hello World escaped {{{$name}}}<br>

The current UNIX timestamp is {{ time() }}.<br>

Not compile: Hello, @{{ name }}.<br>

No escape: Hello, {!! $name !!}.<br>

Default: {{ $name or 'Default' }}<br>




<hr>Verbatim:<hr>
@verbatim


    Commentary {{-- this shouldn't appear --}}<br>

    Hello World {{$name}}<br>

    Hello World escaped {{{$name}}}<br>

    The current UNIX timestamp is {{ time() }}.<br>

    Not compile: Hello, @{{ name }}.<br>

    No escape: Hello, {!! $name !!}.<br>

    Default: {{ $name or 'Default' }}<br>
    @aaaaaa


@endverbatim


<hr>

@if (count($records) === 1)
    I have one record!
@elseif (count($records) > 1)
    I have multiple records!
@else
    I don't have any records!
@endif

<hr>unless:<br>

@unless (false)
    You are not signed in.
@endunless

<h2>New from 1.5</h2>
<pre>
@@set($x1=20)
</pre>
@set($x1=20)
x1={{$x1}}<br>
<pre>
@@set($x1)
</pre>
@set($x1)
x1={{$x1}}<br>
<pre>
@@set($x1=5)
</pre>
@set($x1=5)
x1={{$x1}}<br>
<pre>
@@set($x1='hello')
</pre>
@set($x1='hello')
x1={{$x1}}<br>



<hr>push and stack:<br>

pushing:<br>
@push('scripts')
script1
@endpush
@push('scripts')
script2
@endpush
@push('scripts')
script3
@endpush
@pushonce('scriptonce')
scriptpush1
@endpushonce
@pushonce('scriptonce')
scriptpush2
@endpushonce
@pushonce('scriptonce')
scriptpush3
@endpushonce
<hr>Stack pushed:
@stack('scripts')
<hr>Stack pushed once:
@stack('scriptonce')
<hr>Push inverted:<br>
first stack...<br>
@stack('first')
then push..<br>
@push('first')
after the stack<br>
@endpush




{{'kevinbacon@email.com'}}

@method('PUT')
@method($put)

kevinbacon @@gmail.com
Show all the stack:<br>
stack :  @stack('scripts')
<hr>extra functions<br>
@{{ClassService::Function()}}

<hr>
<pre>
@@compilestamp()
</pre>
<hr>

@compilestamp()<br>
@compilestamp('d-m-y')<br>
<hr>

<hr>
<pre>
@@viewname()
</pre>
<hr>

@viewname('compiled')<br>
@viewname('template')<br>
@viewname('')<br>
<hr>





