<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="http://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.3.0/css/font-awesome.min.css"
          rel="stylesheet" type="text/css">
    <link href="http://pingendo.github.io/pingendo-bootstrap/themes/default/bootstrap.css"
          rel="stylesheet" type="text/css">
</head>
<body>
<div class="section">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">


<h1>Example of BladeOne Extensions (Bootstrap Version)</h1>

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

@commandbutton('boton','v1','Press for Submit','submit',"class='btn-primary'")

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
@selectgroup('id1',"class='object' multiple='multiple' size='5'")
@trio('0','--Select a country--','')
@trios($countries,'id','name','continent',$multipleSelect)
@endselect()
<br>
<br>
Group select (with single selection):<br>
@selectgroup('id1',"class='object' multiple='multiple' size='5'")
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

            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
<script type="text/javascript" src="http://netdna.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>

</body>
</html>
