<?php

namespace eftec\tests;

/**
 * @author G.J.W. Oolbekkink <g.j.w.oolbekkink@gmail.com>
 * @since 17/09/2018
 */
class EachTest extends AbstractBladeTestCase {
    /**
     * @throws \Exception
     */
    public function testEachEmpty() {
        $this->assertEqualsIgnoringWhitespace("", $this->blade->run("each.base", ["list" => []]));
    }

    /**
     * @throws \Exception
     */
    public function testEachMultiple() {
        $this->assertEqualsIgnoringWhitespace("Item 1... Item item...", $this->blade->run("each.base", ["list" => [1, "item"]]));
    }

    /**
     * @throws \Exception
     */
    public function testEachEmptyOption() {
        $this->assertEqualsIgnoringWhitespace("No Item", $this->blade->run("each.empty", ["list" => []]));
    }

    /**
     * @throws \Exception
     */
    public function testEachEmptyMultiple() {
        $this->assertEqualsIgnoringWhitespace("Item Elem... Item item...", $this->blade->run("each.empty", ["list" => ["Elem", "item"]]));
    }
}