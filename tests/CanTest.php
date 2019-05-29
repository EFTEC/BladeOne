<?php

namespace eftec\tests;

/**
 * @author G.J.W. Oolbekkink <g.j.w.oolbekkink@gmail.com>
 * @since  17/09/2018
 */
class CanTest extends AbstractBladeTestCase
{
    /**
     * @throws \Exception
     */
    public function testNoErrorCallback()
    {
        $bladeSource = /** @lang Blade */
            <<<'BLADE'
@error('key')
    Is hidden...
@enderror
BLADE;
        $this->assertEqualsIgnoringWhitespace("", $this->blade->runString($bladeSource, []));
    }

    /**
     * @throws \Exception
     */
    public function testErrorCallback()
    {
        $bladeSource = /** @lang Blade */
            <<<'BLADE'
@error('key')
    Is hidden...
@enderror
BLADE;

        $errorArray = [
            'key' => 'error string'
        ];

        $errorCallback = function($key = null) use ($errorArray) {
            return array_key_exists($key, $errorArray);
        };

        $this->blade->setErrorFunction($errorCallback);

        $this->assertEqualsIgnoringWhitespace("Is hidden...", $this->blade->runString($bladeSource, []));
    }

    /**
     * @throws \Exception
     */
    public function testInlineErrorCallback()
    {
        $bladeSource = /** @lang Blade */
            <<<'BLADE'
<div class="@error('key') extra-class @enderror"></div>
BLADE;

        $errorArray = [
            'key' => 'error string'
        ];

        $errorCallback = function($key = null) use ($errorArray) {
            return array_key_exists($key, $errorArray);
        };

        $this->blade->setErrorFunction($errorCallback);

        $this->assertEquals('<div class=" extra-class "></div>', $this->blade->runString($bladeSource, []));
    }

}