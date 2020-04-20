<!DOCTYPE html>
<html>
<body>
<h1>Example of BladeOne Extensions2</h1>

@label(for="id1" text="hello world:") @input(id="id1" value="hello world$somevar" type="text" )
<hr>
@image(src="https://via.placeholder.com/350x150")
<hr>
@input(id="id1" value="hello world$somevar" type="radio" extra="placeholder='hello123' 555" outer="hi there")
<hr>select:<br>
@select(id="aaa" value=$selection values=$countries alias=$country)
    @item(value='aaa' text='hello world')
    @item(value='aaa' text='hello world')
    @item(value='aaa' text='hello world')
    @items( id="chkx" value=$country->id text=$country->name)
@endselect
<hr>selectgroup:<br>
@select(id="aaa" value=$selection values=$countries alias=$country)
    @optgroup(label="group1")
        @item(value='aaa' text='hello world')
        @item(value='aaa' text='hello world')
        @item(value='aaa' text='hello world')
    @endoptgroup
    @items( value=$country->id text=$country->name optgroup=$country->continent)
@endselect

<br><br>
@select(id="aaa" value=$selection alias=$country multiple="multiple")
    @item(value='aaa' text='hello world')
    @item(value='aaa' text='hello world')
    @item(value='aaa' text='hello world')
    @items(values=$countries value='id' text='name')
@endselect
<hr>

@checkbox(id="idsimple" value="1" checked="1" post="it is a selection")<br>
@checkbox(id="idsimple" value="1" checked="" post="it is a selection")<br>

@radio(id="idsimple" value="1" checked="1" post="it is a selection")<br>
@radio(id="idsimple" value="1" checked="" post="it is a selection")<br>

@textarea(id="aaa" value="3333 3333
aaa3333


")
<br>

@button(value="click me" class="test" onclick='alert("ok")')

<br>
@link(href="https://www.google.cl" text="context")
<br>

@checkboxes(id="checkbox1" value=$selection alias=$country)
    @item(id="aa1" value='aaa' text='hello world' post="<br>")
    @item(id="aa2" value='aaa' text='hello world2' post="<br>")
    @items(values=$countries value='id' text='name' post="<br>")
@endcheckboxes

@radios(id="radios1" name="aaa" value=$selection  alias=$country)
    @item(value='aaa' text='hello world' post="<br>")
    @item(value='aaa' text='hello world2' post="<br>")
    @items(values=$countries value='id' text='name' post="<br>")
@endradios
<br>
@file(name="file" value="123.jpg" post="hello world")
<hr>
@table(class="table" values=$countries alias=$country border="1")
    @tablehead  
        @cell(text="id")
        @cell(text="cod")
        @cell(text="name")
    @endtablehead
    @tablebody(id='hello world'  )
        @tablerow(style="background-color:azure")
            @cell(text=$country->id style="background-color:orange")
            @cell(text=$country->cod )
            @cell(text=$country->name)
        @endtablerow
    @endtablebody
    @tablefooter
        @cell(text="id" colspan="3")
    @endtablefooter
@endtable
<h2>ul</h2>
@ul(id="aaa" value=$selection values=$countries alias=$country)
    @item(value='aaa' text='hello world')
    @item(value='aaa' text='hello world')
    @item(value='aaa' text='hello world')
    @items(value=$country->id text=$country->name)
@endul
<h2>ol</h2>
@ol(id="aaa" value=$selection values=$countries alias=$country)
    @item(value='aaa' text='hello world')
    @item(value='aaa' text='hello world')
    @item(value='aaa' text='hello world')
    @items(value=$country->id text=$country->name)
@endol
<br>
<br>
<br>
<br>
</body>
</html> 