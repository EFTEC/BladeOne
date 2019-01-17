# BladeOneLang extension library (optional)

Requires: BladeOne

This library adds cache to the visual layer and business/logic layer.
For using this library, the code requires to include and use the trait BladeOneCache

Setting:
```php
class MyBlade extends  bladeone\BladeOne {
    use bladeone\BladeOneLang;
}
$blade=new MyBlade($views,$compiledFolder);

$blade->missingLog='c:\temp\missingkey.txt'; // (optional) if a traduction is missing the it will be saved here.

$lang='jp'; // try es,jp or fr
include './lang/'.$lang.'.php';
```

Where /lang/es.php is simmilar to:   

```php 
<?php

use eftec\bladeone\BladeOneLang;

BladeOneLang::$dictionary=array(
    'Hat'=>'Sombrero'
    ,'Cat'=>'Gato'
    ,'Cats'=>'Gatos' 
    ,'%s is a nice cat'=>'%s es un buen gato'
);
```
Template file
```php 
Hat in spanish is @_e('Hat')<br>
There is one @_n('Cat','Cats',1)<br>
@_ef('%s is a nice cat','Cheshire')<br>
```
Returns:    
Hat in spanish is Sombrero   
There is one Gato   
Cheshire es un buen gato.   





- Where MyBlade is a new class that extends the bladeone class and use the Lang features.



## Template methods

### @_e('Word or phrase')

it tries to translate the word if its in the array defined by `BladeOneLang::$dictionary`.
If there is not a entry with the word 'Hat' (case sensitive) then it returns 'Hat'. Also, if the log file is define, the it also saves an entry with the missing word.

For the previous example. `@_e('Hat')` returns Sombrero.

### @_ef('some phrase %s another words %s %i','word1','word2',20)

Its the same than `@_e`, however it parses the text (using `sprintf`).   
If the operation fails then, it returns the original expression without translation.

For the previous example.` @_ef('%s is a nice cat','Cheshire')`  returns Cheshire es un buen gato.

### @_n('Singular','Plural',number)

If number is plural (more than 1) then it translates (if any) the second word, otherwise it translates the first word.
If not number is used then it always translates the singular expression.

For the previous example.` @_n('Cat','Cats',100)`  returns Cheshire es un buen gato.

