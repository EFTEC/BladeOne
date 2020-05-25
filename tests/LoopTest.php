<?php

namespace eftec\tests;

/**
 * @author G.J.W. Oolbekkink <g.j.w.oolbekkink@gmail.com>
 * @since 17/09/2018
 */
class LoopTest extends AbstractBladeTestCase
{
    /**
     * @throws \Exception
     */
    public function testFor()
    {
        $bladeSource = /** @lang Blade */
            <<<'BLADE'
@for($i = 1; $i <= 6; $i++) 
    Nr. {{ $i }}
@endfor
BLADE;
        $this->assertEqualsIgnoringWhitespace("Nr. 1 Nr. 2 Nr. 3 Nr.4 Nr. 5 Nr. 6", $this->blade->runString($bladeSource, []));
    }

    /**
     * @throws \Exception
     */
    public function testForEach()
    {
        $bladeSource = /** @lang Blade */
            <<<'BLADE'
@foreach($items as $item)
    Item {{ $item }}
@endforeach
BLADE;
        $this->assertEqualsIgnoringWhitespace("Item a Item b", $this->blade->runString($bladeSource, ['items' => ['a', 'b']]));
    }
    /**
     * @throws \Exception
     */
    public function testForEachLoopVar()
    {
        $bladeSource = /** @lang Blade */
            <<<'BLADE'
@foreach($items as $item)
    Item_index:{{$loop->index}},iteration:{{$loop->iteration}},first:{{$loop->first}}
    ,last:{{$loop->last}},remaining:{{$loop->remaining}}
    ,count:{{$loop->count}},depth:{{$loop->depth}},parent:{{$loop->parent}}
    ,even:{{$loop->even}},odd:{{$loop->odd}},value{{ $item }}
@endforeach
BLADE;
        $this->assertEqualsIgnoringWhitespace("Item_index:0,iteration:1,first:1"."
        ,last:,remaining:2"."
        ,count:2,depth:1,parent:"."
        ,even:1,odd:,valuea"."
        Item_index:1,iteration:2,first:"."
        ,last:1,remaining:1,count:2,depth:1,parent:"."
        ,even:,odd:1,valueb", $this->blade->runString($bladeSource, ['items' => ['a', 'b']]));
    }
    /**
     * @throws \Exception
     */
    public function testForElse()
    {
        $bladeSource = /** @lang Blade */
            <<<'BLADE'
@forelse($items as $item)
    Item {{ $item }}
@empty
    No Items
@endforelse
BLADE;
        $this->assertEqualsIgnoringWhitespace("Item a Item b", $this->blade->runString($bladeSource, ['items' => ['a', 'b']]));
        $this->assertEqualsIgnoringWhitespace("No Items", $this->blade->runString($bladeSource, ['items' => []]));
    }

    /**
     * @throws \Exception
     */
    public function testWhile()
    {
        $bladeSource = <<<'BLADE'
@php($i = 1)
@while($i < 5)
    @php($i++)
    Item {{ $i }}
@endwhile
BLADE;
        $this->assertEqualsIgnoringWhitespace("Item 2 Item 3 Item 4 Item 5", $this->blade->runString($bladeSource, []));
    }
}
