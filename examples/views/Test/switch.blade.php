<h1>test switch where $i={!! $i !!}, $j={!! $j !!}</h1>
@switch($i)
    @case(0)
    Zero case for i...
        @switch ($j)
            @case (1)
                First case for j...
            @break
            @default
                Default case for j...
            @break
        @endswitch
    @break

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
<h1>test switch</h1>
@switch($j)
    @case(1)
    First case...
    @break
    @case(44)
    44 case...
    @break
    @case(0)
    0 case...
    @break
    @default
    Default case...
@endswitch