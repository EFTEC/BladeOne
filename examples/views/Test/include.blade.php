<div>
    @@include('shared.errors')<br>
    @include('shared.errors')

    <form>
        <!-- Form Contents -->
    </form>
    <hr>alias include using @@input:<br>
    <hr>Global Variable: {{$globalme}}<br>
    @input()<br>
    @input(['type' => 'email','value'=>'billgates@microsoft.com'])<br>
    <hr>alias include using @@input2:<br>
    @input2()<br>
    @input2(['type' => 'email','value'=>'billgates@microsoft.com'])<br>
    
    <hr>
</div>