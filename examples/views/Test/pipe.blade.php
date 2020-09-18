<h1>Example of the pipes (filter)</h1>
Remember that pipeEnable must set in true<br>
<pre>
    $blade->pipeEnable=true;
</pre>

<h2>$name </h2>
{{$name}}<br>

<h2>Testing default value</h2>
{!!  $name | "default"  !!}<br>
{!! $name2 | "default"  !!}<br>
{!! $name2 | 'default'  !!}<br>
{!! $name2 | $othername  !!}<br>
{!! $name2 | 555 !!}<br>

<h2>$name | strtolower | strtolower | strtolower </h2>
{{$name | strtolower| strtolower | strtolower}}<br>

<h2>$date (DateTime) | format:'Y/m/d h:i:s' </h2>
{{$date | format:'Y/m/d h:i:s'}}<br>

<h2>50.4 | format:'%08.4f' </h2>
{{50.4 | format:'%08.4f'}}<br>


<h2>$date | format:'Y/m/d h:i:s' </h2>
{{$date | format:'Y/m/d h:i:s'}}<br>


<h2>$name | strtolower | substr:0,4</h2>
{{$name | strtolower| substr:0,4}}<br>

<h2>$name | strtolower | substr:0,4 + $name | substr:5| strtoupper</h2>
<pre>// substr(strtolower($name ),0,4) . strtoupper(substr($name ,5))</pre>
{{$name | strtolower| substr:0,4}} {{$name | substr:5| strtoupper }}<br> 


<h2>$name | strtoupper </h2>
{{$name | strtoupper }}<br>

<h2>$name | strtoupper | strtolower (strtoupper is executed initially) then lower </h2>
{{$name | strtoupper | strtolower }}<br>

<h2>$name| strtoupper | substr:0,4 (no escaped)</h2>
{!! $name| strtoupper | substr:0,4 !!}<br>

<h2>$name| method1 (method added by directive)</h2>
{!! $name| method1 !!}<br>


<h2>$name| method2 (global function)</h2>
{!! $name| method2 !!}<br>

