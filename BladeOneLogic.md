#BladeOneLogic extension library (optional)

Requires: BladeOne

For using this tag, the code requires to use the class BladeOneLogic
 
## Defintion of Blade Template
For using this tag, the code requires to use the class BladeOneLogic that extends the class BladeOne.
The code extends the class BladeOneHtml by creating a daisy chain.

### switch / case

_Example:(the indentation is not required)_
```html
@switch($countrySelected)
    @case(1)
        first country selected<br>
    @case(2)
        second country selected<br>
    @defaultcase()
        other country selected<br>
@endswitch()
```

- @switch. The first value is the variable to evaluate.
- @case. Indicates the value to compare.  It should be runs inside a @switch/@endswitch
- @defaultcase. (optional) If not case is the correct then the block of @defaultcase is evaluated.
- @endswitch. End the switch.