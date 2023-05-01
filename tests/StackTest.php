<?php
namespace eftec\tests;

/**
 * @author G.J.W. Oolbekkink <g.j.w.oolbekkink@gmail.com>
 * @since 16/09/2018
 */
class StackTest extends AbstractBladeTestCase {
    /**
     * @throws \Exception
     */
    public function testSwitch(): void
    {
        $bladeSource = /** @lang Blade */
            <<<'BLADE'
@push('mystack_first')hello
@endpush()
@pushonce('mystack_first')hello2
@endpushonce()
@pushonce('mystack_first')hello3
@endpushonce()
@pushonce('mystack_second')hello4
@endpushonce()
first:@stack('mystack_first',"notfound")
all:@stack('mystack_*',"notfound")
notfound:@stack('mystack_notfound',"notfound")
BLADE;
            $this->assertEqualsIgnoringWhitespace("first:hellohello2all:hellohello2hello4notfound:notfound"
                , $this->blade->runString($bladeSource, ['i' => 1]));
    }
}
