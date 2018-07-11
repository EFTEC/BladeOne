<h1>Test for version 2.4 and higher</h1>

@json($records);

<h2>test of isset</h2>
@isset($name)
$name is defined
@endisset()
<h2>test of include when</h2>
@includewhen(true,'Test2.include',['v1'=>'hello'])
<h2>Test of includefirst</h2>
@includeFirst(['Test2.nope', 'Test2.include'], ['v1' => 'hello'])

<h2>prepend</h2>



@push('scripts')
    This will be second...
@endpush


@prepend('scripts')
This will be first...
@endprepend


<hr>
@stack('scripts')
<hr>

<h2>empty test</h2>

@forelse ($records as $rec)
    <li>{{ $rec }}</li>
@empty
    <p>No record</p>
@endforelse

@forelse ($emptyArray as $user)
    <li>{{ $user->name }}</li>
@empty
    <p>No users</p>
@endforelse

<h2> empty($value) test</h2>

@empty($emptyArray)
    $emptyArray is empty
@endempty

<h2> Test append</h2>

