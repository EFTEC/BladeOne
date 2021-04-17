<?php
namespace eftec\tests;

use http\Exception\RuntimeException;

/**
 * @author G.J.W. Oolbekkink <g.j.w.oolbekkink@gmail.com>
 * @since 16/09/2018
 */
class ThrowTest extends AbstractBladeTestCase
{
    /**
     * @throws \Exception
     */
    public function testThrow()
    {
        $this->blade->throwOnError=true;
        $error=false;
        try {
            $this->blade->run('notexist', ['i' => 1]);
        } catch (\Exception $ex) {
            $error=true;
        }
        self::assertEquals(true, $error);
        $this->blade->throwOnError=false;
        self::assertContains("BladeOne Error [getFile]", $this->blade->run('notexist', ['i' => 1]));
    }
}
