<?php

namespace eftec\tests;

/**
 * @author Jorge Castro
 * @since 16/09/2018
 */
class LangTest extends AbstractBladeTestCase
{
    /**
     * @throws \Exception
     */
    public function testlang()
    {
        $blade=$this->blade; // $blade is instance of $this->blade
        include __DIR__."/lang/es.php";
        $bladeSource="@_n('Cat','Cats',1)";
        $this->assertEqualsIgnoringWhitespace("Gato", $this->blade->runString($bladeSource, []));

        $bladeSource="@_n('Cat','Cats',100)";
        $this->assertEqualsIgnoringWhitespace("Gatos", $this->blade->runString($bladeSource, []));

        $bladeSource="@_n('Cat','Cats',100)";
        $this->assertEqualsIgnoringWhitespace("Gatos", $this->blade->runString($bladeSource, []));

        $bladeSource="@_n('Hat','Hats',100)"; // There is not a translation for hats.
        $this->assertEqualsIgnoringWhitespace("Hats", $this->blade->runString($bladeSource, []));

        $bladeSource="@_ef('%s is a nice cat','Cheshire')";
        $this->assertEquals("Cheshire es un buen gato", $this->blade->runString($bladeSource, []));

        $bladeSource="@_e('Hat')";
        $this->assertEquals("Sombrero", $this->blade->runString($bladeSource, []));
    }
}
