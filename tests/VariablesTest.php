<?php

namespace eftec\tests;

/**
 * Test passing variables into Blade templates.
 *
 * @author G.J.W. Oolbekkink <g.j.w.oolbekkink@gmail.com>
 * @since 17/09/2018
 */
class VariablesTest extends AbstractBladeTestCase {
    /**
     * @throws \Exception
     */
    public function testPrintVariable() {
        $bladeString = /** @lang Blade */
            <<<'BLADE'
{{$var1}}
BLADE;
        $this->assertEqualsIgnoringWhitespace("content", $this->blade->runString($bladeString, ["var1" => "content"]));
        $this->assertEqualsIgnoringWhitespace("content2", $this->blade->runString($bladeString, ["var1" => "content2"]));
        $this->assertEqualsIgnoringWhitespace("&lt;a href=&quot;/&quot;&gt;My Link&lt;/a&gt;", $this->blade->runString($bladeString, ["var1" => "<a href=\"/\">My Link</a>"]));
    }

    /**
     * @throws \Exception
     */
    public function testPrintUnescapedVariable() {
        $bladeString = /** @lang Blade */
            <<<'BLADE'
{!! $var1 !!}
BLADE;
        $this->assertEqualsIgnoringWhitespace("content", $this->blade->runString($bladeString, ["var1" => "content"]));
        $this->assertEqualsIgnoringWhitespace("<a href=\"/\">My Link</a>", $this->blade->runString($bladeString, ["var1" => "<a href=\"/\">My Link</a>"]));
    }
}