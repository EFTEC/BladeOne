<!DOCTYPE html>
<html>
<body>
<h1>Example of BladeOne Extensions</h1>

<h2>This example show the use of extensions</h2>

<hr>Input:<br>
@label('id','Select the value:')&nbsp;
@input('id','default value','text')<br>
@label('id','Select the value:')&nbsp;
@input('id','default value','text',['class'=>'x1','enabled'=>'disable'])
<br>
<br>textarea:<br>
@textarea('idtext','it is an example\nmoreexample')
<br>
<br>hidden:<br>
@hidden('hidfield','some value')
<hr>Button:<br>

@commandbutton('boton','v1','Press for Submit')

<hr>Select:<br>

Code:<br>
<pre>
@@select('id1',"class='object'")<br>
@@item('0','--Select a country--')<br>
@@items($countries,'id','name',$countrySelected)<br>
@@endselect()<br>
</pre>
<br><br>

Simple select:<br>
@select('id1',"class='object'")
    @item('0','--Select a country--')
    @items($countries,'id','name',$countrySelected)
@endselect()
<br>
<br>
Multi select:<br>
@select('id1',"class='object' multiple='multiple'")
    @item('0','--Select a country--')
    @items($countries,'id','name',$multipleSelect)
@endselect()
<br>
<br>
Group select (with multi selection):<br>
@selectgroup('id1',"class='object' multiple='multiple', size='5'")
    @trio('0','--Select a country--','')
    @trios($countries,'id','name','continent',$multipleSelect)
@endselect()
<br>
<br>
Group select (with single selection):<br>
@selectgroup('id1',"class='object' multiple='multiple', size='5'")
    @trio('0','--Select a country--','')
    @trios($countries,'id','name','continent',$countrySelected)
@endselect()
<br>
<br>
Radio simple:<br>
@radio('idsimple','777','SelectMe','777')
<br>
@radio('idsimple','777','Not selected','778')
<br>
<br>
checkbox simple:<br>
@checkbox('idsimple','777','SelectMe','777')
<br>
@checkbox('idsimple','777','Not selected','778')
<br>
<br>
Radios list:<br>
@radio('id2')
    @item('0','--Select a country--')<br>
    @items($countries,'id','name',$countrySelected,'%s<br>')
@endradio()
<br>
<br>
Checkboxes list:<br>
@checkbox('id3')
    @item('0','--Select a country--')<br>
    @items($countries,'id','name',$countrySelected,'%s<br>')
@endcheckbox()
<br>
<br>
Checkboxes with multi selections:<br>
@checkbox('id4')
    @item('0','--Select a country--')<br>
    @items($countries,'id','name',$multipleSelect,'%s<br>')
@endcheckbox()
<hr>
<h3>New components for html</h3>
<pre>
@ listboxes('idlistbox',$countries,'id','name',$multipleSelect)
</pre>
<br>
@listboxes('idlistbox',$countries,'id','name',$multipleSelect)

<hr>
<h2>Test of class BladeOneLogic $countrySelected={!! $countrySelected !!}</h2>
Code:<br>
<pre>
@@switch($countrySelected)
@@case(1)
first country selected<br>
@break
@@case(2)
second country selected<br>
@break
@@default
other country selected<br>
@@endswitch()
</pre>
<br><br>
@switch($countrySelected)
@case(1)
    first country selected<br>
@break
@case(2)
    second country selected<br>
@break
@default
    other country selected<br>
@endswitch

<hr><h2>Link</h2>
<pre>
@@link('http://www.google.com','Go to google')
</pre>
@link('http://www.google.com','Go to google')
</body>
</html>
