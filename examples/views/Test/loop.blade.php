<html>
<head>
    <title>Example of Loop</title>
    <!-- Google Fonts -->
    <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Roboto:300,300italic,700,700italic">
    <!-- CSS Reset -->
    <link rel="stylesheet" href="//cdn.rawgit.com/necolas/normalize.css/master/normalize.css">
    <!-- Milligram CSS minified -->
    <link rel="stylesheet" href="//cdn.rawgit.com/milligram/milligram/master/dist/milligram.min.css">
</head>
<body>
<h1>Example of Loops</h1>

<h2>@@for</h2>
<pre>
@@for ($i = 0; $i < 10; $i++)
    The current value is @{{ $i }}
@@endfor
</pre>
@for ($i = 0; $i < 10; $i++)
    The current value is {{ $i }}<br>
@endfor

<br><h2>@@foreach</h2>
<pre>
@@foreach ($users as $user)
    This is user @{{ $user->id }}
@@endforeach
</pre>

@foreach ($users as $user)
    This is user {{ $user->id }}<br>
@endforeach
<br><h2>@@splitforeach</h2>
<em>Split a foreach cycle by showing a text each "x" number of elements.
    The text is not displayed if its the last element of the list</em><br>

<pre>
   &lt;table border=&quot;1&quot;&gt;
    &lt;tr&gt;
    @@foreach ($drinks7 as $drink)
        &lt;td&gt;@{{$drink}}&lt;/td&gt;
        @@splitforeach(2,'&lt;/tr&gt;&lt;tr&gt;','&lt;/tr&gt;&lt;tr&gt;&lt;td colspan=2&gt;end of the table&lt;/td&gt;&lt;/tr&gt;')
        @@endforeach
    &lt;/table&gt;
</pre>
<table border="1">
    <tr>
        @foreach ($drinks7 as $drink)
            <td>{{$drink}}</td>
    @splitforeach(2,'</tr><tr>','</tr><tr><td colspan=2>end of the table</td></tr>')
    @endforeach
</table>

with even number of elements: (with using the end parameter) <br>
<pre>
    &lt;table border=&quot;1&quot;&gt;
    &lt;tr&gt;
    @@foreach ($drinks8 as $drink)
        &lt;td&gt;@{{$drink}}&lt;/td&gt;
        @@splitforeach(2,'&lt;/tr&gt;&lt;tr&gt;','&lt;/tr&gt;&lt;tr&gt;&lt;td colspan=2&gt;end of the table&lt;/td&gt;&lt;/tr&gt;')
    @@endforeach
    &lt;/table&gt;
</pre>
<table border="1">
    <tr>
        @foreach ($drinks8 as $drink)
            <td>{{$drink}}</td>
    @splitforeach(2,'</tr><tr>','</tr><tr><td colspan=2>end of the table</td></tr>')
    @endforeach
</table>
A more complex exercise, using variable <br>
<pre>
    &lt;table border=&quot;1&quot;&gt;
    @@set($even=0)
    &lt;tr&gt;&lt;td style=&quot;background-color:lightblue&quot;&gt;@{{$even/2+1}}&lt;/td&gt;

    @@foreach ($drinks8 as $drink)
        @@set($even)
        @@if($even % 4 ==0 || ($even+1) % 4 ==0)
            @@set($stylerow=&quot;#ffffff&quot;)
            @@else
            @@set($stylerow=&quot;#909090&quot;)
            @@endif
        &lt;td style=&quot;background-color:@{{$stylerow}}&quot;&gt;@{{$drink}}&lt;/td&gt;
        @@splitforeach(2,'&lt;/tr&gt;&lt;tr&gt;&lt;td style=&quot;background-color:lightblue&quot;&gt;'.($even/2+1).'&lt;/td&gt;','&lt;/tr&gt;&lt;tr&gt;&lt;td colspan=3&gt;end of the table&lt;/td&gt;&lt;/tr&gt;')
        @@endforeach
    &lt;/table&gt;
</pre>
<table border="1">
    @set($even=0)
    <tr><td style="background-color:lightblue">{{$even/2+1}}</td>

        @foreach ($drinks8 as $drink)
            @set($even)
            @if($even % 4 ==0 || ($even+1) % 4 ==0)
                @set($stylerow="#ffffff")
            @else
                @set($stylerow="#909090")
            @endif
            <td style="background-color:{{$stylerow}}">{{$drink}}</td>
    @splitforeach(2,'</tr><tr><td style="background-color:lightblue">'.($even/2+1).'</td>','</tr><tr><td colspan=3>end of the table</td></tr>')
    @endforeach
</table>

<br><h2>@@forelse</h2>

<pre>
@@forelse ($users as $user)
    &lt;li&gt;@{{ $user-&gt;name }}&lt;/li&gt;
@@empty
    &lt;p&gt;No users&lt;/p&gt;
@@endforelse
</pre>
@forelse ($users as $user)
    <li>{{ $user->name }}</li>
@empty
    <p>No users</p>
@endforelse

<br><h2>@@while</h2>
<pre>
    @@set($whilecounter=0)
    @@while ($whilecounter&lt;3)
        @@set($whilecounter)
        I'm looping forever.&lt;br&gt;
    @@endwhile
</pre>
@set($whilecounter=0)
@while ($whilecounter<3)
    @set($whilecounter)
    I'm looping forever.<br>
@endwhile

<br><h2>@@continue/break (foreach)</h2>
<pre>
    @@foreach ($users as $user)
        @@if($user-&gt;type == 1) // ignores the first user John Smith
        @@continue
        @@endif
        &lt;li&gt;@{{ $user->type }} - @{{ $user-&gt;name }}&lt;/li&gt;

        @@if($user-&gt;number == 5) // ends the cycle.
            @@break
        @@endif
    @@endforeach
</pre>
@foreach ($users as $user)
    @if($user->type == 1)
        @continue
    @endif
    {{ $user->type }} - {{ $user->name }}<br>
    @if($user->number == 5)
        @break
    @endif
@endforeach
<pre>
    @@foreach ($users as $user)
        @@continue($user-&gt;type == 1)
        @{{ $user->type }} - @{{ $user-&gt;name }}&lt;/br&gt;
        @@break($user-&gt;number == 5)
        @@endforeach
</pre>

@foreach ($users as $user)
    @continue($user->type == 1)
    {{ $user->type }} -{{ $user->name }}</br>
    @break($user->number == 5)
@endforeach
<br><h2>@@each</h2>
<pre>
    @@each('Test.InnerView.name', $records, 'job')
</pre>
where Test.InnerView.name contains
<pre>
    &lt;hr&gt;
    @{{$job}}
    &lt;hr&gt;
</pre>

@each('Test.InnerView.name', $records, 'job')
</body>
</html>