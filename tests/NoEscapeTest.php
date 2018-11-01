<?php

namespace eftec\tests;
use eftec\bladeone\BladeOne;

/**
 * @author Jorge
 * @since 2018-nov-1 6:17 PM
 */
class NoEscapeTest extends AbstractBladeTestCase {
    /**
     * @throws \Exception
     */
    public function testCompilationDebugCreatesCompiledFile() {
        $this->blade->setMode(BladeOne::MODE_DEBUG);
        $this->blade->run('compilation.noescape', []);
        $this->assertFileEquals(__DIR__ . '/resources/compiled/compilation.noescape.bladec',
            __DIR__ . '/resources/templates/compilation/noescape.blade.php');
        $this->blade->setMode(BladeOne::MODE_SLOW);
    }

}