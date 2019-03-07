<div>
    @include('shared.errors')

    <form>
        <!-- Form Contents -->
    </form>
    <hr>alias include using @@input:<br>
    @input()
    @input(['type' => 'email','value'=>'billgates@microsoft.com'])<br>
    <hr>alias include using @@input2:<br>
    @input2()
    @input2(['type' => 'email','value'=>'billgates@microsoft.com'])<br>
    
    <hr>
</div>