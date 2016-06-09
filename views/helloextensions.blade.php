<!DOCTYPE>
<html>
<body>
<h1>Example of BladeOne Extensions</h1>

<h2>This example show the use of extensions</h2>

<hr>Input:<br>

@input('id','default value','text')

@commandbutton('boton','v1','Press for Submit')

<hr>Select:<br>

Code:<br>
<pre>
@@selectonemenu('id1',"class='object'")<br>
@@selectitem('0','--Select a country--')<br>
@@selectitems($countries,'id','name',$countrySelected)<br>
@@endselectonemenu()<br>
</pre>
<br><br>


@selectonemenu('id1',"class='object'")
    @selectitem('0','--Select a country--')
    @selectitems($countries,'id','name',$countrySelected)
@endselectonemenu()
<hr>
<h2>Test of class BladeOneLogic</h2>
Code:<br>
<pre>
@@switch($countrySelected)
@@case(1)
first country selected<br>
@@case(2)
second country selected<br>
@@defaultcase()
other country selected<br>
@@endswitch()
</pre>
<br><br>
@switch($countrySelected)
@case(1)
    first country selected<br>
@case(2)
    second country selected<br>
@defaultcase()
    other country selected<br>
@endswitch()

</body>
</html>