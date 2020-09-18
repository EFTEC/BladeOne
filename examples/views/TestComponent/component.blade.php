<h1>It is a test of the tags @@component</h1>
@component('TestComponent.alert',array('title'=>'COMPONENT #1','color'=>"red"))
    <strong>Whoops!</strong> Something went wrong! (the code is right btw)
@endcomponent

@component('TestComponent.alert',['color'=>'orange'])
    @slot('title')
        <hr>
        COMPONENT #2
        @component('TestComponent.alert',['color'=>'yellow'])
            @slot('title')
                COMPONENT #2.1
            @endslot
            <strong>Whoops!</strong> Something went wrong! (the code is right btw)
        @endcomponent
        
        <hr>
    @endslot
    <strong>Whoops!</strong> Something went wrong! (the code is right btw)
@endcomponent

@component('TestComponent.alert',['color'=>'lightblue'])
    @slot('title', 'COMPONENT #3')

    <strong>Whoops!</strong> Something went wrong! (the code is right btw)
@endcomponent