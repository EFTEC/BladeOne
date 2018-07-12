<h1>test switch where $i={!! $i !!}</h1>
@switch($i)
    @case(1)
    First case...
    @break

    @case(2)
    Second case...
    @break

    @default
    Default case...
@endswitch

<h1>test switch</h1>
@switch($i)
    @case(1)
    First case...
    @break

    @case(44)
    44 case...
    @break

    @default
    Default case...
@endswitch