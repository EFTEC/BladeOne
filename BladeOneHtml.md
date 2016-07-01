#BladeOneHtml extension library (optional)

Requires: BladeOne

For using this tag, the code requires to use the strait BladeOneHtml
```php
class myBlade extends  bladeone\BladeOne {
    use bladeone\BladeOneHtml;
}

$blade=new myBlade($templateFolder,$compiledFolder);
// ...
echo $blade->run("template",$values);
```

##New Tags

### Select

```html
@select('id1')
    @item('0','--Select a country--',$countrySelected,'')
    @items($countries,'id','name',$countrySelected,'')
@endselect()
```

- @select($name,[$extra]) 
-   _Generates a select tag._
-   $name is the id/name of the tag.
-   $extra (optional) is optional parameter (see note).

![select](http://i.imgur.com/yaMavQB.jpg?1)

### EndSelect
End of the @select or @selectgroup

- @endselect() 
-   _End the select tag._

### SelectGroup

```html
@selectgroup('id1',"class='object' multiple='multiple', size='5'")
    @trio('0','--Select a country--','')
    @trios($countries,'id','name','continent',$multipleSelect)
@endselect()
```

- @selectgroup($name,[$extra]) 
-   _Generates a select tag._
-   $name is the id/name of the tag.
-   $extra (optional) is optional parameter (see note).


### Item
(See select example)

-   @item($valueId, $valueText, [$selectedItem],[$wrapper],[$extra])
-   _Show an item_
-   $valueId value of the item (value not visible)
-   $valueText visible text of the item
-   $selectedItem id of the selected item (or items).
-   $wrapper it evolves each item in a tag. For example '<div>%s</div>'
-   $extra (optional) is optional parameter (see note).

### Items
(See select example)

- @items($arrValues, $fieldId, $fieldText, [$selectedItem], [$wrapper], [$extra]) 
-   _List a list(in arrvalues) of items_
-   $arrValues values to show.  The values should be an array of objects or another arrays.
-   $fieldId  Field of the id value.  Example IdCountry= $country['IdCountry'] or $country->IdCountry
-   $fieldText Field of the visible value.
-   $selectedItem id of the selected item (or items).
-   $wrapper it evolves each item in a tag. For example '<div>%s</div>'
-   $extra (optional) is optional parameter (see note).

### Trio
(See selectgroup example)

-   @trio($valueId, $valueText,$value3, [$selectedItem],[$wrapper],[$extra])
-   _Show an item that uses 3 values_
-   $valueId value of the item (value not visible)
-   $valueText visible text of the item
-   $value3 Third value used for multiple purpose.
-   $selectedItem id of the selected item (or items).
-   $wrapper it evolves each item in a tag. For example '<div>%s</div>'
-   $extra (optional) is optional parameter (see note).

### Trios
(See selectgroup example)

- @trios($arrValues, $fieldId, $fieldText,$fieldThird, [$selectedItem], [$wrapper], [$extra]) 
-   _List a list(in arrvalues) of trio_
-   $arrValues values to show.  The values should be an array of objects or another arrays.
-   $fieldId  Field of the id value.  Example if $fieldId="IdCountry" then $country['IdCountry'] or $country->IdCountry
-   $fieldText Field of the visible value.
-   $fieldText Field of the third value.
-   $selectedItem id of the selected item (or items).
-   $wrapper it evolves each item in a tag. For example '<div>%s</div>'
-   $extra (optional) is optional parameter (see note).

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

### EndForm
End of the @endform 

- @endform() 
-   _End the endform tag._


### Others (not yet documented but up and running)

- radio
- checkbox
- endradio
- endcheckbox
- textarea
- hidden
- label
- commandbutton
- listboxes (pending: javascript).


### NOTE: Extra Parameter
 
Additionally, you can add an (optional) last parameter with additional value (see the example of @select)

```html
 <!-- code using bootstrap -->
 <div class="form-group">
  <label for="sel1">Select list:</label>
        @select('id1')
            @item('0','--Select a country--',"",class='form-control'")
            @items($countries,'id','name',"",$countrySelected)
        @endselect()
  </select>
</div>
```

##Bootstrap extension (optionally)

Optionally, we can add a extension compatible with Bootstrap 3.  It replaces the tags with the bootstrap ones.

For use, you must use the trait BladeOneHtmlBootstrap:
```php
class myBlade extends  bladeone\BladeOne {
    use bladeone\BladeOneHtmlBootstrap;
}
$blade=new myBlade($templateFolder,$compiledFolder);
// ...
echo $blade->run("template",$values);
```

> Note: The template requires to link manually the css and js files of jqueryui and bootstrap.

###Comparison between Bootstrap and plain html

![Image](http://i.imgur.com/svJxAEg.jpg)

_Without bootstrap_

![Image](http://i.imgur.com/DOjUeOv.jpg)

_With bootstrap_


##Version

- 2016-07-01 1.5 @listboxes
