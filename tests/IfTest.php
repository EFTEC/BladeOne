<?php

namespace eftec\tests;

/**
 * @author G.J.W. Oolbekkink <g.j.w.oolbekkink@gmail.com>
 * @since 16/09/2018
 */
class IfTest extends AbstractBladeTestCase {
    /**
     * @throws \Exception
     */
    public function testIf() {
        $bladeSource = /** @lang Blade */
            <<<'BLADE'
@if($a == 4)
    First case...
@endif
Always executed...
BLADE;
        $this->assertEqualsIgnoringWhitespace("First case...Always executed...", $this->blade->runString($bladeSource, ['a' => 4]));
        $this->assertEqualsIgnoringWhitespace("Always executed...", $this->blade->runString($bladeSource, ['a' => 5]));
    }

    /**
     * @throws \Exception
     */
    public function testElse() {
        $bladeSource = /** @lang Blade */
            <<<'BLADE'
@if($a == 4)
    True case...
@else
    False case...
@endif
BLADE;
        $this->assertEqualsIgnoringWhitespace("True case...", $this->blade->runString($bladeSource, ['a' => 4]));
        $this->assertEqualsIgnoringWhitespace("False case...", $this->blade->runString($bladeSource, ['a' => 3]));
    }

    /**
     * @throws \Exception
     */
    public function testElseIf() {
        $bladeSource = /** @lang Blade */
            <<<'BLADE'
@if($a == 4)
    First true case...
@elseif($a == 5)
    Second true case...
@else
    False case...
@endif
BLADE;
        $this->assertEqualsIgnoringWhitespace("First true case...", $this->blade->runString($bladeSource, ['a' => 4]));
        $this->assertEqualsIgnoringWhitespace("Second true case...", $this->blade->runString($bladeSource, ['a' => 5]));
        $this->assertEqualsIgnoringWhitespace("False case...", $this->blade->runString($bladeSource, ['a' => 0]));
    }

    /**
     * @throws \Exception
     */
    public function testUnless() {
        $bladeSource = /** @lang Blade */
            <<<'BLADE'
@unless($a == 4)
    False case...
@endunless
BLADE;
        $this->assertEqualsIgnoringWhitespace("", $this->blade->runString($bladeSource, ['a' => 4]));
        $this->assertEqualsIgnoringWhitespace("False case...", $this->blade->runString($bladeSource, ['a' => 3]));
    }

    /**
     * @throws \Exception
     */
    public function testIsset() {
        $bladeSource = /** @lang Blade */
            <<<'BLADE'
@isset($a)
    A is set...
@endisset
BLADE;
        $this->assertEqualsIgnoringWhitespace("", $this->blade->runString($bladeSource, []));
        $this->assertEqualsIgnoringWhitespace("A is set...", $this->blade->runString($bladeSource, ['a' => 3]));
    }

    /**
     * @throws \Exception
     */
    public function testEmpty() {
        $bladeSource = /** @lang Blade */
            <<<'BLADE'
@empty($a)
    A is empty...
@endempty
BLADE;
        $this->assertEqualsIgnoringWhitespace("", $this->blade->runString($bladeSource, ['a' => [3]]));
        $this->assertEqualsIgnoringWhitespace("A is empty...", $this->blade->runString($bladeSource, ['a' => []]));

    }

    /**
     * @throws \Exception
     */
    public function testInvalidIf() {
        $this->expectException(\Exception::class);

        /** @noinspection BladeControlDirectives */
        $bladeSource = /** @lang Blade */
            <<<'BLADE'
@if($a == 4)
BLADE;
        $this->blade->runString($bladeSource, ['a' => 4]);
    }
}
