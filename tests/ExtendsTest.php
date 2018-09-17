<?php

namespace eftec\tests;

/**
 * @author G.J.W. Oolbekkink <g.j.w.oolbekkink@gmail.com>
 * @since 17/09/2018
 */
class ExtendsTest extends AbstractBladeTestCase {
    /**
     * @throws \Exception
     */
    public function testExtends() {
        $this->assertEqualsIgnoringWhitespace("Child... Base...", $this->blade->run("extends.child", []));
    }

    /**
     * @throws \Exception
     */
    public function testExtendsWithSection() {
        $this->assertEqualsIgnoringWhitespace("Base... From Child...", $this->blade->run("extends.child_section", []));
    }
}