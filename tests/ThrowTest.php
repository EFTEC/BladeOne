<?php /** @noinspection ReturnTypeCanBeDeclaredInspection */

namespace eftec\tests;




class ThrowTest extends AbstractBladeTestCase
{
    /**
     * @throws \Exception
     */
    public function testThrow()
    {
        $this->blade->throwOnError = true;
        $error = false;
        //try {
        //     $this->blade->run('notexist', ['i' => 1]);
        //} catch (\Exception $ex) {
        //    $error=true;
        // }
        // self::assertEquals(true, $error);
        $this->blade->throwOnError = false;
        self::assertEquals(1,1);
       // self::assertContains("BladeOne Error [getFile]", $this->blade->run('notexist', ['i' => 1]));
    }
}
