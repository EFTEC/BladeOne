<?php
namespace eftec\tests;

/**
 * @author G.J.W. Oolbekkink <g.j.w.oolbekkink@gmail.com>
 * @since 16/09/2018
 */
class SwitchTest extends AbstractBladeTestCase {
    /**
     * @throws \Exception
     */
    public function testSwitch() {
        $bladeSource = /** @lang Blade */
            <<<'BLADE'
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
BLADE;
            $this->assertEqualsIgnoringWhitespace("First case...", $this->blade->runString($bladeSource, ['i' => 1]));
            $this->assertEqualsIgnoringWhitespace("Second case...", $this->blade->runString($bladeSource, ['i' => 2]));
            $this->assertEqualsIgnoringWhitespace("Default case...", $this->blade->runString($bladeSource, ['i' => 3]));
    }
}