<h1>It is a test of the tags @@component</h1>
<h2>paramless</h2>

@component('TestComponent.paramless')
    <strong>Whoops!</strong> Something went wrong! (the code is right btw), it must show a no-color background
@endcomponent

@component('TestComponent.paramless')
    <strong>Whoops!</strong> Something went wrong! (the code is right btw), it must show a no-color background
@endcomponent


<h2>with parameters</h2>
@component('TestComponent.alert',array('title'=>'no title'))
    <strong>Whoops!</strong> Something went wrong! (the code is right btw), it must show a no-color background
@endcomponent
<br>

@component('TestComponent.alert',array('title'=>'COMPONENT #1','color'=>"red"))
    <strong>Whoops!</strong> Something went wrong! (the code is right btw), it must show a red background
@endcomponent

@component('TestComponent.alert',['color'=>'orange'])
    @slot('title')
        COMPONENT #2
    @endslot
        <hr>

        @component('TestComponent.alert',['color'=>'yellow'])
            @slot('title')
                COMPONENT #2.1
            @endslot
            <strong>Whoops!</strong> Something went wrong! (the code is right btw), it must show a yellow background
                <hr>
                COMPONENT #3 starts here:
                @component('TestComponent.alert',['color'=>'lightblue'])
                    @slot('title')
                        COMPONENT #3.1
                    @endslot
                    <strong>Whoops!</strong> Something went wrong! (the code is right btw), it must show a lightblue background
                @endcomponent

                <hr>
        @endcomponent
        <hr>

    <strong>Whoops!</strong> Something went wrong! (the code is right btw), iit must show a orange background
@endcomponent

@component('TestComponent.alert',['color'=>'lightblue'])
    @slot('title', 'COMPONENT #3')

    <strong>Whoops!</strong> Something went wrong! (the code is right btw), it must show a lightblue background
@endcomponent

@component('TestComponent.alert',[])
    @slot('title', 'COMPONENT #3')

    <strong>Whoops!</strong> Something went wrong! (the code is right btw), it must show a no-color background
@endcomponent
