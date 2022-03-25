<?php

namespace eftec\tests;

/**
 * Test passing variables into Blade templates.
 *
 * @author G.J.W. Oolbekkink <g.j.w.oolbekkink@gmail.com>
 * @since 17/09/2018
 */
class VariablesTest extends AbstractBladeTestCase
{
    /**
     * @throws \Exception
     */
    public function testPrintVariable()
    {
        $bladeString = /** @lang Blade */
            <<<'BLADE'
{{$var1}}
BLADE;
        $this->assertEqualsIgnoringWhitespace("content", $this->blade->runString($bladeString, ["var1" => "content"]));
        $this->assertEqualsIgnoringWhitespace("content2", $this->blade->runString($bladeString, ["var1" => "content2"]));
        $this->assertEqualsIgnoringWhitespace("&lt;a href=&quot;/&quot;&gt;My Link&lt;/a&gt;", $this->blade->runString($bladeString, ["var1" => "<a href=\"/\">My Link</a>"]));
    }
    public function testSetFunction()
    {
        $bladeString = '@set($info=funcion(1,222+funcion(2,3,4),"abc",3))';
        self::assertEquals('<?php $info=@funcion(1,222+funcion(2,3,4),"abc",3);?>', $this->blade->compileString($bladeString));
    }
    public function testSet()
    {
        $bladeString='@set($info=$abc)';
        self::assertEquals('<?php $info=@$abc;?>', $this->blade->compileString($bladeString));

        $bladeString='@set($info=fn("aaa",30))';
        self::assertEquals('<?php $info=@fn("aaa",30);?>', $this->blade->compileString($bladeString));

        $bladeString='@set($info=44+55)';
        self::assertEquals('<?php $info=@44+55;?>', $this->blade->compileString($bladeString));

        $bladeString='@set($info=$r["dd"])';
        self::assertEquals('<?php $info=@$r["dd"];?>', $this->blade->compileString($bladeString));

        $bladeString='@set($info=$vm[\'_fummedicionesEmpty\'])';
        self::assertEquals('<?php $info=@$vm[\'_fummedicionesEmpty\'];?>', $this->blade->compileString($bladeString));

        $bladeString='@set($info)';
        self::assertEquals('<?php $info++;?>', $this->blade->compileString($bladeString));

        $bladeString='@set($info)';
        self::assertEquals('<?php $info++;?>', $this->blade->compileString($bladeString));

    }

    /**
     * @throws \Exception
     */
    public function testPrintUnescapedVariable()
    {
        $bladeString = /** @lang Blade */
            <<<'BLADE'
{!! $var1 !!}
BLADE;
        $this->assertEqualsIgnoringWhitespace("content", $this->blade->runString($bladeString, ["var1" => "content"]));
        $this->assertEqualsIgnoringWhitespace("<a href=\"/\">My Link</a>", $this->blade->runString($bladeString, ["var1" => "<a href=\"/\">My Link</a>"]));
    }
    /**
     * @throws \Exception
     */
    public function testPrintPipe()
    {


        $this->blade->pipeEnable=true;
        $this->assertEquals('<?php echo md5(substr(substr(substr(substr(substr(substr(strtoupper($name ),1,5 ),1,10 ),1,15 ),1,20 ),1,25 ),1,30 )); ?>'
            , $this->blade->compileString('{!! $name | md5 | substr:1,30 | substr:1,25 | substr:1,20 | substr:1,15 | substr:1,10 | substr:1,5 | strtoupper !!}',["name" => "12345"]));
        $this->assertEquals('<?php echo md5(md5(md5(substr($name ,1,25)))); ?>'
            , $this->blade->compileString('{!! $name | md5 | md5 | md5 | substr:1,25 !!}',["name" => "12345"]));

        $this->assertEquals("<?php echo md5(substr(\$name ,1,2)); ?>"
            , $this->blade->compileString('{!! $name | md5 | substr:1,2 !!}',["name" => "12345"]));
        $this->assertEquals("<?php echo md5(substr(substr(\$name ,1,2),1,2 )); ?>"
            , $this->blade->compileString('{!! $name | md5 | substr:1,2 | substr:1,2  !!}',["name" => "12345"]));
        $this->assertEquals("<?php echo md5(\$name ); ?>"
            , $this->blade->compileString('{!! $name | md5 !!}', ["name" => "12345"]));
    }


    /**
     * @throws \Exception
     */
    public function testDontPrintVariable()
    {
        $bladeString = /** @lang Blade */
            <<<'BLADE'
@{{ $var }}
BLADE;
        $this->assertEqualsIgnoringWhitespace("{{ \$var }}", $this->blade->runString($bladeString, ["var" => "my_var"]));
    }

    /**
     * @throws \Exception
     */
    public function testVerbatim()
    {
        $bladeString = /** @lang Blade */
            <<<'BLADE'
@verbatim
{{ $var }}
@endverbatim
BLADE;
        $this->assertEqualsIgnoringWhitespace("{{ \$var }}", $this->blade->runString($bladeString, ["var" => "my_var"]));
    }
}
