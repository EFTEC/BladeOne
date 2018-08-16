<h1>Custom If </h1>
<h2>Check if $i={{$i}} is negative</h2>
@isnegative($i)
// $i is negative
@elseisnegative(-$i)
// $i is not negative
@else
// Is is anything
@endisnegative
<h2>Check if $e={{$e}} is negative</h2>
@isnegative($e)
// $e is negative
@elseisnegative(-$e)
// $e is not negative
@else
// Is is anything
@endisnegative
<h2>Check if $i={{$i}} is equals to 5</h2>
@isequals($i,5)
// $i is equals to 5
@elseisequals($i,3)
// $i is equals to 3
@else
// $is is not 5 nor 3
@endisequals
<h2>Check if $e={{$e}} is equals to 5</h2>
@isequals($e,5)
// $e is equals to 5
@elseisequals($e,3)
// $e is equals to 3
@else
// $e is not 5 nor 3
@endisequals
