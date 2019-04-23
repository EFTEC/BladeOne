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
    	$this->blade->share('globalme','global!');
        $this->assertEqualsIgnoringWhitespace("Child(global!)...Base(global!)...", $this->blade->run("extends.child", []));
    }

    /**
     * @throws \Exception
     */
    public function testExtendsWithSection() {
	    $this->blade->share('globalme','global!');
        $this->assertEqualsIgnoringWhitespace("Base(global!)...FromChild(global!)...", $this->blade->run("extends.child_section", []));
    }
}