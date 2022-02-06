<?php

namespace eftec\tests;

class NullEscapeTest extends AbstractBladeTestCase
{
    public function test_returns_empty_string_when_escaping_null_value()
    {
        $this->assertEquals(
            '',
            $this->blade::e(null)
        );
    }
}
