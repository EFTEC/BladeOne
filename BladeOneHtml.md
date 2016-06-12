#BladeOneHtml extension library (optional)

Requires: BladeOne

For using this tag, the code requires to use the class BladeOneHtml

##New Tags

### Select

```html
@selectonemenu('id1')
    @selectitem('0','--Select a country--')
    @selectitems($countries,'id','name',$countrySelected)
@endselectonemenu()
```

@selectonemenu creates the **select** tag. The first value is the id and name of the tag.
@selectitem allows to add one element **option** tag. The first value is the id and the second is the visible text
@selectitems allows to add one list of elements **option** tag. The first value is the list of values, the second and third is the id and name. And the fourth one is the selected value (optional)
    
![select](http://i.imgur.com/yaMavQB.jpg?1)

### Input

```html
@input('iduser',$currentUser,'text'[,$extra])
```

@input creates a **input** tag. The first value is the id/name, the second is the default value, the third is the type (by default is text for textbox)*[]: 

### Form
```form
@form(['action'],['post'][,$extra])
    ... form goes here
@endform
```
@form creates **form** html tag. The first value (optional) is the action, the second value (optional) is the method ('post','get')

### NOTE: Extra Parameter
 
Additionally, you can add an (optional) last parameter with additional value (see the example of @selectonemenu)

```html
 <!-- code using bootstrap -->
 <div class="form-group">
  <label for="sel1">Select list:</label>
        @selectonemenu('id1')
            @selectitem('0','--Select a country--',"class='form-control'")
            @selectitems($countries,'id','name',$countrySelected)
        @endselectonemenu()
  </select>
</div>
```

