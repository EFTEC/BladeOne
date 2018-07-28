<h1>{!! $title !!}.</h1>
<h2>Current user: @user</h2>
<br>
@@auth('admin')
    <hr>
@auth('admin')
    account is adminstrator
@elseauth('user')
    account is user
@elseauth()
    account is not administrator neither user
@endauth
<br><br>
@@can('edit','noallowed'), @@elsecan('view'), @@elsecan(), @@endcan() By code, nobody is allowed
<hr>
@can('edit','noallowed')
    The user can edit!
@elsecan('view')
    The user can edit but he can view
@elsecan()
    The user can't edit neither view.
@endcan()
<br><br>
@@cannot('edit','noallowed'),@@elsecannot('delete') By code, nobody is allowed
<hr>
@cannot('edit','noallowed')
    The user is not allowed to edit the element not allowed
@elsecannot('delete')
    The user is not allowed to edit the element not allowed, but delete
@elsecannot()
    The user is allowed to edit
@endcannot()
<br><br>
@@cannot('edit')
<hr>
@cannot('edit')
    The user is not allowed to edit
@elsecannot('delete')
    The user is allowed to edit but delete
@elsecannot()
    The user is allowed to edit
@endcannot()
<br><br>
@@can('edit')
<hr>
@can('edit')
    The user can edit!
@elsecan()
    The user can't edit!
@endcan()
<br><br>
@@canany(['edit','add'])
<hr>
@canany(['edit','add'])
    The user can edit or add!
@elsecan
    The user can't edit or add!
@endcanany()
<br><br>
@@guest()
<hr>
@guest()
    User is anonymous
@elseguest('admin')
    User is not anonymous and is not admin
@elseguest()
    User is not anonymous
@endguest()

<br><br>
@@auth()
<hr>
@auth()
    User is not anonymous
@elseauth()
    User is anonymous
@endauth()