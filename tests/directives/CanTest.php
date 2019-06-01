<?php

namespace eftec\tests\directives;

use eftec\tests\AbstractBladeTestCase;

/**
 * @author Jake Whiteley <jakebwhiteley@gmail.com>
 * @since  01/06/2019
 */
class CanTest extends AbstractBladeTestCase
{
    /**
     * @throws \Exception
     */
    public function testCan()
    {
        $bladeSource = /** @lang Blade */
            <<<'BLADE'
@can('read')
    Auth content...
@endcan
BLADE;
        $this->blade->setAuth("user1", null, ['read']);
        $this->assertEqualsIgnoringWhitespace("Auth content...", $this->blade->runString($bladeSource, []));

        $this->blade->setAuth(null);
        $this->assertEqualsIgnoringWhitespace("", $this->blade->runString($bladeSource, []));
    }

    /**
     * @throws \Exception
     */
    public function testElsecan()
    {
        $bladeSource = /** @lang Blade */
            <<<'BLADE'
@can('read')
    can read
@elsecan('write')
    can write
@elsecan
    denied
@endcan
BLADE;
        $this->blade->setAuth("user1", null, ['write']);
        $this->assertEqualsIgnoringWhitespace("can write", $this->blade->runString($bladeSource, []));

        $this->blade->setAuth("user1");
        $this->assertEqualsIgnoringWhitespace("denied", $this->blade->runString($bladeSource, []));
    }

    /**
     * @throws \Exception
     */
    public function testCannot()
    {
        $bladeSource = /** @lang Blade */
            <<<'BLADE'
@cannot('read')
    Auth content...
@endcannot
BLADE;
        $this->blade->setAuth(null);
        $this->assertEqualsIgnoringWhitespace("Auth content...", $this->blade->runString($bladeSource, []));

        $this->blade->setAuth("user1", null, ['read']);
        $this->assertEqualsIgnoringWhitespace("", $this->blade->runString($bladeSource, []));
    }

    /**
     * @throws \Exception
     */
    public function testElsecannot()
    {
        $bladeSource = /** @lang Blade */
            <<<'BLADE'
@cannot('read')
    you cannot read
@elsecannot('write')
    you cannot write
@elsecannot
    denied
@endcannot
BLADE;

        $this->blade->setAuth(null, null, ['read']);
        $this->assertEqualsIgnoringWhitespace("you cannot write", $this->blade->runString($bladeSource, []));

        $this->blade->setAuth(null, null, ['read', 'write']);
        $this->assertEqualsIgnoringWhitespace("denied", $this->blade->runString($bladeSource, []));
    }

    /**
     * @throws \Exception
     */
    public function testCanany()
    {
        $bladeSource = /** @lang Blade */
            <<<'BLADE'
@canany(['read'])
    Auth content...
@endcanany
BLADE;
        $this->blade->setAuth("user1", null, ['read']);
        $this->assertEqualsIgnoringWhitespace("Auth content...", $this->blade->runString($bladeSource, []));

        $this->blade->setAuth(null);
        $this->assertEqualsIgnoringWhitespace("", $this->blade->runString($bladeSource, []));
    }

    /**
     * @throws \Exception
     */
    public function testElseCanany()
    {
        $bladeSource = /** @lang Blade */
            <<<'BLADE'
@canany(['read'])
    can read
@elsecanany(['write'])
    can write
@elsecanany
    denied
@endcanany
BLADE;
        $this->blade->setAuth("user1", null, ['write']);
        $this->assertEqualsIgnoringWhitespace("can write", $this->blade->runString($bladeSource, []));

        $this->blade->setAuth("user1", null, null);
        $this->assertEqualsIgnoringWhitespace("denied", $this->blade->runString($bladeSource, []));
    }

    /**
     * @throws \Exception
     */
    public function testCustomCanCallbackCanBeSet()
    {
        $this->blade->setCanFunction(function($action, $subject) {
            if ($action === 'read' && $subject === 42) {
                return true;
            }
        });

        $bladeSource = /** @lang Blade */
            <<<'BLADE'
@can('read', 42)
    Auth content...
@endcan
BLADE;

        $this->assertEqualsIgnoringWhitespace("Auth content...", $this->blade->runString($bladeSource, []));
    }

    /**
     * @throws \Exception
     */
    public function testCustomCananyCallbackCanBeSet()
    {
        $this->blade->setAnyFunction(function($actions, $subject) {
            if (\in_array('read', $actions) && $subject === 42) {
                return true;
            }
        });

        $bladeSource = /** @lang Blade */
            <<<'BLADE'
@canany(['read'], 42)
    Auth content...
@endcanany
BLADE;

        $this->assertEqualsIgnoringWhitespace("Auth content...", $this->blade->runString($bladeSource, []));
    }
}