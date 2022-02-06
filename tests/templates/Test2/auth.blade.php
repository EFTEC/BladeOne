Title:{!! $title !!}.
Current user: @user
@auth('admin')
account is adminstrator.
@elseauth('user')
account is user.
@elseauth()
account is not administrator neither user.
@endauth
@can('edit','noallowed')
The user can edit!.
@elsecan('view')
The user can edit but he can view.
@elsecan()
The user cant edit neither view.
@endcan()
@cannot('edit','noallowed')
The user is not allowed to edit the element not allowed.
@elsecannot('delete')
The user is not allowed to edit the element not allowed, but delete.
@elsecannot()
The user is allowed to edit.
@endcannot()
@cannot('edit')
The user is not allowed to edit.
@elsecannot('delete')
The user is allowed to edit but delete.
@elsecannot()
The user is allowed to edit.
@endcannot()
@can('edit')
The user can edit.
@elsecan()
The user cant edit.
@endcan()
@canany(['edit','add'])
The user can edit or add.
@elsecan
The user cant edit or add.
@endcanany()
@guest()
User is anonymous.
@elseguest('admin')
User is not anonymous and is not admin.
@elseguest()
User is not anonymous.
@endguest()
@auth()
User is not anonymous.
@elseauth()
User is anonymous.
@endauth()
