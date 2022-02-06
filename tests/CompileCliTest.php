<?php


namespace eftec\tests;

use eftec\bladeone\BladeOne;
use Exception;


class CompileCliTest extends AbstractBladeTestCase
{
    /** @noinspection OnlyWritesOnParameterInspection
     * @noinspection PhpArrayWriteIsNotUsedInspection
     */
    public function testCompilation(): void
    {
        global $argv;
        $argv[]='-check';
        $this->blade->cliEngine();
        $this->assertEquals(true,$this->blade->checkHealthPath());
        $argv=[];
        $argv[]='-clearcompile';
        $this->blade->cliEngine();
        $this->assertEquals(0,$this->blade->clearcompile());


    }
}
