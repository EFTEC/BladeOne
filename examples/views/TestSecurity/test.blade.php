<h1> test auth and guest </h1>
@auth
    The user is authenticated...
@endauth

@guest
    The user is not authenticated... (guest)
@endguest

<hr>

@auth('admin')
    // The user is authenticated as admin...
@endauth

@guest('admin')
    // The user is not authenticated as admin... (guest)
@endguest

<h1>can todo</h1>

@can('update', $post)
    <!-- The Current User Can Update The Post -->
@elsecan('create', App\Post::class)
    <!-- The Current User Can Create New Post -->
@endcan

@cannot('update', $post)
    <!-- The Current User Can't Update The Post -->
@elsecannot('create', App\Post::class)
    <!-- The Current User Can't Create New Post -->
@endcannot