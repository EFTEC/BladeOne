<hr>
@select(id=1,name=helloworld,class=$class,extra='hola "mundo',inner='<h1>hola "mundo</h1>',value=$curUser)
@item(value=aaa,display="select...1",end='',class='someclass 22')
@item(value="aaa",display="select...2",end='')
@item(value=$class,display="select...3",end='')
@items(values=$users,fielddisplay=name,fieldvalue=id,fieldgroup=type)
@endselect
<hr>

@input(type=text,name='txt1',value='hello" world',placeholder="it is a placeholder",onclick="alert('hello');")
<br>
@input(type=text,name='txt1',value='hello" world',placeholder="it is a placeholder",onclick='alert("hello");')
<hr>

@textarea(name='text2',value="<h1>it is text area</h1>")



@label(for=helloworld,value="hola mundo",inner="<b>hello</b> world")



<hr>

@checkboxes(id=1,name=helloworld,class=$class,extra='hola "mundo',inner='<h1>hola "mundo</h1>',value=$curUser)
@item(value=$class,display="--select ,checkbox--",end='<br>')
@items(values=$users,fielddisplay=name,fieldvalue=id,end='<br>')
@endcheckbox

<hr>

@checkboxes(id=1,name=helloworld2,class=$class,extra='hola "mundo',inner='<h1>hola "mundo</h1>',value=$curUsers)
@item(value=$class,display="--select ,checkbox--",end='<br>')
@items(values=$users,fielddisplay=name,fieldvalue=id,end='<br>')
@endcheckbox

<hr>

<hr>

@radios(id=1,name=helloworld3,class=$class,extra='hola "mundo',inner='<h1>hola "mundo</h1>',value=$curUser)
@item(value=$class,display=Empty,end='<br>')
@items(values=$users,fielddisplay=name,fieldvalue=id,end='<br>')
@endradio

<hr>

@radios(id=1,name=helloworld4,class=$class,extra='hola "mundo',inner='<h1>hola "mundo</h1>',value=$curUser)
@item(value=$class,display=Empty)
@items(values=$users,fielddisplay=name,fieldvalue=id)
@endradio