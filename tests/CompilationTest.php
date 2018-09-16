<?php

namespace eftec\tests;
use eftec\bladeone\BladeOne;

/**
 * @author G.J.W. Oolbekkink <g.j.w.oolbekkink@gmail.com>
 * @since 16/09/2018
 */
class CompilationTest extends AbstractBladeTestCase {
    /**
     * @throws \Exception
     */
    public function testCompilation() {
        $this->assertEqualsIgnoringWhitespace("Compilation test template", $this->blade->run('compilation', []));
    }

    /**
     * @throws \Exception
     */
    public function testCompilationInSubdir() {
        $this->assertEqualsIgnoringWhitespace("Compilation test template in subdir", $this->blade->run('subdir.compilation', []));
    }

    /**
     * @throws \Exception
     */
    public function testCompilationCreatesCompiledFile() {
        $this->blade->run('compilation', []);

        $this->assertFileExists(__DIR__ . '/resources/compiled/' . sha1('compilation') . '.bladec');
    }

    /**
     * @throws \Exception
     */
    public function testCompilationDebugCreatesCompiledFile() {
        $this->blade->setMode(BladeOne::MODE_DEBUG);
        $this->blade->run('compilation', []);

        $this->assertFileExists(__DIR__ . '/resources/compiled/compilation.bladec');

        $this->blade->setMode(BladeOne::MODE_SLOW);
    }

    /**
     * @throws \Exception
     */
    public function testCompilationCustomFileExtension() {
        $this->blade->setFileExtension('.blade');

        $this->assertEqualsIgnoringWhitespace("Custom extension blade file", $this->blade->run('compilation', []));

        $this->blade->setFileExtension('.blade.php');
    }

    /**
     * @throws \Exception
     */
    public function testCompilationCustomCompileExtension() {
        $this->blade->setCompiledExtension('.bladeD');
        $this->blade->run('compilation', []);

        $this->assertFileExists(__DIR__ . '/resources/compiled/' . sha1('compilation') . '.bladeD');

        $this->blade->setCompiledExtension('.bladec');
    }
}