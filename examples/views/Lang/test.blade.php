<hr>
Translation
<hr>
@@_e('Hat') :<br>
@_e('Hat')<br><br>

@@_e('Not yet translated') :<br>
@_e('Not yet translated')<br><br>

@@_n('Cat','Cats',1) :<br>
@_n('Cat','Cats',1)<br><br>

@@_n('Cat','Cats',100) :<br>
@_n('Cat','Cats',100)<br><br>

@@_n('Hat','Hats',100) :<br>
@_n('Hat','Hats',100) (note, there is not a translation for Hats)<br><br>

@@_ef('%s is a nice cat','Cheshire') :<br>
@_ef('%s is a nice cat','Cheshire')<br><br>
<hr>
Test methods with missing fields
<hr>
@@_n('Cat','Cats') : (if number is missing then its returned as a singular)<br>
@_n('Cat','Cats') <br><br>

@@_ef('%s is a nice cat') : (if a field is missing the it returns the expression)<br>
@_ef('%s is a nice cat')<br>
