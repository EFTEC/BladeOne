@use(\mynamespace\SomeClass)
<h1>Calling #1 @@myfunction :</h1>
<hr>
@myfunction
<h1>Calling #2 @@myfunction?ddd :</h1>
<hr>
@myfunction

<hr>
<hr>
@SomeClass::method("with at")
<hr>
<h1>SomeClass::method("id","name","value")</h1>
<hr>
{!! SomeClass::method("id","name","value") !!}



<hr>
<hr>
@SomeClass::method
<hr>