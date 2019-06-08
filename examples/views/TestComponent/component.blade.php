hola mundo
@component('TestComponent.alert',array('title'=>'hello'))
    <strong>Whoops!</strong> Something went wrong! (the code is right btw)
@endcomponent

@component('TestComponent.alert')
    @slot('title')
        hello 2
    @endslot
    <strong>Whoops!</strong> Something went wrong! (the code is right btw)
@endcomponent

@component('TestComponent.alert')
    @slot('title', 'hello 3')

    <strong>Whoops!</strong> Something went wrong! (the code is right btw)
@endcomponent