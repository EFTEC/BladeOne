<?php

namespace eftec\tests;

/**
 * @author G.J.W. Oolbekkink <g.j.w.oolbekkink@gmail.com>
 * @since 17/09/2018
 */
class IncludeTest extends AbstractBladeTestCase
{
    /**
     * @throws \Exception
     */
    public function testInclude()
    {
        $this->assertEqualsIgnoringWhitespace("First... Included... Second...", $this->blade->run("include.base", []));
    }

    /**
     * based on bug #68
     * @throws \Exception
     */
    public function testIncludeAlias()
    {
        $this->blade->addInclude('Shared.input', 'input');
        $expected='<input type="text" value=""><input type="email" value="billgates@microsoft.com">';
        $html=$this->blade->run("include.includealias", []);
        $this->assertEquals($expected, $html);

        $this->blade->addInclude('Shared.input');
        $expected='<input type="text" value=""><input type="email" value="billgates@microsoft.com">';
        $html=$this->blade->run("include.includealias", []);
        $this->assertEquals($expected, $html);
    }
    
    
    /**
     * @throws \Exception
     */
    public function testIncludeIf()
    {
        //$this->markTestIncomplete("Broken, see #54");
        // Note, includeif only includes if the template exists. The If is not a conditional include
        // (via variable) but to include if the template exists.

        $this->assertEqualsIgnoringWhitespace("First... Included... Second...", $this->blade->run("include.if", ["some_var" => false]));
        $this->assertEqualsIgnoringWhitespace("First... Second...", $this->blade->run("include.ifcorrupt", []));
    }

    /**
     * @throws \Exception
     */
    public function testIncludeWhen()
    {
        $this->assertEqualsIgnoringWhitespace("First... Included... Second...", $this->blade->run("include.when", ["should_include" => true]));
        $this->assertEqualsIgnoringWhitespace("First... Second...", $this->blade->run("include.when", ["should_include" => false]));
    }
}
