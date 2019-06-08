<?php

namespace eftec\tests\directives;

use eftec\tests\AbstractBladeTestCase;

/**
 * @author Jake Whiteley <jakebwhiteley@gmail.com>
 * @since  01/06/2019
 */
class ErrorTest extends AbstractBladeTestCase
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
    public function testErrorMessage()
    {
        $bladeSource = /** @lang Blade */
            <<<'BLADE'
@error('key')
    {{ $message }}
@enderror
BLADE;

        $errorArray = [
            'key' => 'error string'
        ];

        $errorCallback = function($key = null) use ($errorArray) {
            if (array_key_exists($key, $errorArray)) {
                return $errorArray[$key];
            }

            return false;
        };

        $this->blade->setErrorFunction($errorCallback);

        $this->assertEqualsIgnoringWhitespace("error string", $this->blade->runString($bladeSource, []));
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