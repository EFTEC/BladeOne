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