<?php

namespace eftec\tests;

/**
 * @author G.J.W. Oolbekkink <g.j.w.oolbekkink@gmail.com>
 * @since 17/09/2018
 */
class AuthTest extends AbstractBladeTestCase {
    public function tearDown() {
        $this->blade->setAuth(null);
    }

    /**
     * @throws \Exception
     */
    public function testEmptyAuth() {
        $bladeSource = /** @lang Blade */
            <<<'BLADE'
@auth
    Auth content...
@endauth
BLADE;
        $this->blade->setAuth("user1");
        $this->assertEqualsIgnoringWhitespace("Auth content...", $this->blade->runString($bladeSource, []));

        $this->blade->setAuth(null);
        $this->assertEqualsIgnoringWhitespace("", $this->blade->runString($bladeSource, []));
    }

    /**
     * @throws \Exception
     */
    public function testEmptyGuest() {
        $bladeSource = /** @lang Blade */
            <<<'BLADE'
@guest
    Guest content...
@endguest
BLADE;
        $this->blade->setAuth("user1");
        $this->assertEqualsIgnoringWhitespace("", $this->blade->runString($bladeSource, []));

        $this->blade->setAuth(null);
        $this->assertEqualsIgnoringWhitespace("Guest content...", $this->blade->runString($bladeSource, []));
    }

    /**
     * @throws \Exception
     */
    public function testAuthWithParam() {
        $bladeSource = /** @lang Blade */
            <<<'BLADE'
@auth('admin')
    Auth content...
@endauth
BLADE;
        $this->blade->setAuth("user1");
        $this->assertEqualsIgnoringWhitespace("", $this->blade->runString($bladeSource, []));

        $this->blade->setAuth("user1", "admin");
        $this->assertEqualsIgnoringWhitespace("Auth content...", $this->blade->runString($bladeSource, []));

        $this->blade->setAuth(null);
        $this->assertEqualsIgnoringWhitespace("", $this->blade->runString($bladeSource, []));
    }

    /**
     * @throws \Exception
     */
    public function testGuestWithParam() {
        $bladeSource = /** @lang Blade */
            <<<'BLADE'
@guest('admin')
    Guest content...
@endguest
BLADE;
        $this->blade->setAuth("user1");
        $this->assertEqualsIgnoringWhitespace("Guest content...", $this->blade->runString($bladeSource, []));

        $this->blade->setAuth("user1", "admin");
        $this->assertEqualsIgnoringWhitespace("", $this->blade->runString($bladeSource, []));

        $this->blade->setAuth(null);
        $this->assertEqualsIgnoringWhitespace("Guest content...", $this->blade->runString($bladeSource, []));
    }
}