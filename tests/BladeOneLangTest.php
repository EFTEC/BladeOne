<?php

namespace eftec\tests;

use eftec\bladeone\BladeOne;
use eftec\bladeone\BladeOneLang;

/**
 * @author Jake Whiteley <jakebwhiteley@gmail.com>
 * @since  01/06/2019
 *
 * @property BladeOne blade
 */
class BladeOneLangTest extends AbstractBladeTestCase
{
    protected function setUp()
    {
        $this->blade = new BladeOne(
            self::TEMPLATE_PATH,
            self::COMPILED_PATH,
            BladeOne::MODE_SLOW
        );

        BladeOne::$dictionary = [
            'Hat' => 'Sombrero',
            'Cat' => 'Gato',
            'Cats' => 'Gatos',
            '%s is a nice cat' => '%s es un buen gato',
            'There are %d %s cats' => 'hay %d %s gatos'
        ];

        copy(
            \realpath(dirname(__FILE__) . '/resources/DummyLogContent.txt'),
            \realpath(dirname(__FILE__) . '/resources/fullDummyLog.txt')
        );
    }

    /**
     * @throws \Exception
     */
    public function test_eDirective()
    {
        $this->assertEquals(
            "Hat in spanish is Sombrero",
            $this->blade->runString("Hat in spanish is @_e('Hat')")
        );

        $this->assertEquals(
            "Dog in spanish is Dog",
            $this->blade->runString("Dog in spanish is @_e('Dog')")
        );
    }

    /**
     * @throws \Exception
     */
    public function test_nDirective()
    {
        $this->assertEquals(
            "There is one Gato",
            $this->blade->runString("There is one @_n('Cat','Cats',1)")
        );

        $this->assertEquals(
            "There is one Gato",
            $this->blade->runString("There is one @_n('Cat','Cats',-10)")
        );

        $this->assertEquals(
            "There is one Gatos",
            $this->blade->runString("There is one @_n('Cat','Cats',2)")
        );

        $this->assertEquals(
            "There is one Dog",
            $this->blade->runString("There is one @_n('Dog','Dogs', 1)")
        );
    }

    /**
     * @throws \Exception
     */
    public function test_efDirective()
    {
        $this->assertEquals(
            "Cheshire es un buen gato",
            $this->blade->runString("@_ef('%s is a nice cat','Cheshire')")
        );

        $this->assertEquals(
            "hay 2 nice gatos",
            $this->blade->runString("@_ef('There are %d %s cats', 2, 'nice')")
        );

        $this->assertEquals(
            "there is no translation",
            $this->blade->runString("@_ef('there is no %s', 'translation')")
        );
    }

    /**
     * @throws \Exception
     */
    public function testLogFileIsWrittenTo()
    {
        $file = tmpfile();
        $this->blade->missingLog = stream_get_meta_data($file)['uri'];

        // Run a translation which should generate a log
        $this->blade->runString("Dog in spanish is @_e('Dog')");

        // Should not write anything additional
        $this->blade->runString("Hat in spanish is @_e('Hat')");

        $this->assertStringMatchesFormatFile($this->blade->missingLog, "Dog\n");
    }

    /**
     * @throws \Exception
     */
    public function testNonStringsAreWrittenToLog()
    {
        $file = tmpfile();
        $this->blade->missingLog = stream_get_meta_data($file)['uri'];

        $data = [
            'array' => ['Dog']
        ];

        $this->blade->runString('Dog in spanish is @_e($array)', $data);

        $this->assertStringMatchesFormatFile(
            $this->blade->missingLog,
            "Array\n(\n    [0] => Dog\n)\n\n"
        );
    }

    /**
     * @throws \Exception
     */
    public function testLargeLogFilesAreOverwritten()
    {
        $this->blade->missingLog = realpath(dirname(__FILE__) . '/resources/fullDummyLog.txt');

        $this->assertLessThan(\filesize($this->blade->missingLog), 100000);
        $this->blade->runString("Dog in spanish is @_e('Dog')");
        \clearstatcache();

        $this->assertEquals(\filesize($this->blade->missingLog), 4);
    }
}

