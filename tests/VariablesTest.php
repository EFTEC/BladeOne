<?php

namespace eftec\tests;

use Exception;

/**
 * Test passing variables into Blade templates.
 *
 * @author G.J.W. Oolbekkink <g.j.w.oolbekkink@gmail.com>
 * @since 17/09/2018
 */
class VariablesTest extends AbstractBladeTestCase
{
    /**
     * @throws Exception
     */
    public function testPrintVariable(): void
    {
        $bladeString = /** @lang Blade */
            <<<'BLADE'
{{$var1}}
BLADE;
        $this->assertEqualsIgnoringWhitespace("content", $this->blade->runString($bladeString, ["var1" => "content"]));
        $this->assertEqualsIgnoringWhitespace("content2", $this->blade->runString($bladeString, ["var1" => "content2"]));
        $this->assertEqualsIgnoringWhitespace("&lt;a href=&quot;/&quot;&gt;My Link&lt;/a&gt;", $this->blade->runString($bladeString, ["var1" => "<a href=\"/\">My Link</a>"]));
    }
    public function testSetFunction(): void
    {
        $bladeString = '@set($info=funcion(1,222+funcion(2,3,4),"abc",3))';
        self::assertEquals('<?php $info=@funcion(1,222+funcion(2,3,4),"abc",3);?>', $this->blade->compileString($bladeString));
    }
    public function testSet(): void
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

        $bladeString='@set($info=1===2)';
        self::assertEquals('<?php $info=@1===2;?>', $this->blade->compileString($bladeString));
    }

    /**
     * @throws Exception
     */
    public function testNull(): void
    {

        $bladeString='var:{{$var}},{{$var2}}';
        self::assertEquals('var:,', $this->blade->runString($bladeString,['var'=>null]));
    }



    /**
     * @throws Exception
     */
    public function testPrintUnescapedVariable(): void
    {
        $bladeString = /** @lang Blade */
            <<<'BLADE'
{!! $var1 !!}
BLADE;
        $this->assertEqualsIgnoringWhitespace("content", $this->blade->runString($bladeString, ["var1" => "content"]));
        $this->assertEqualsIgnoringWhitespace("<a href=\"/\">My Link</a>", $this->blade->runString($bladeString, ["var1" => "<a href=\"/\">My Link</a>"]));
    }
    /**
     * @throws Exception
     */
    public function testPrintPipe(): void
    {


        $this->blade->pipeEnable=true;
        $this->assertEquals('<?php echo strtoupper(substr(substr(substr(substr(substr(substr(md5($name ),1,30 ),1,25 ),1,20 ),1,15 ),1,10 ),1,5 )); ?>'
            , $this->blade->compileString('{!! $name | md5 | substr:1,30 | substr:1,25 | substr:1,20 | substr:1,15 | substr:1,10 | substr:1,5 | strtoupper !!}'));
        $this->assertEquals('<?php echo substr(md5(md5(md5($name ))),1,25); ?>'
            , $this->blade->compileString('{!! $name | md5 | md5 | md5 | substr:1,25 !!}',["name" => "12345"]));

        $this->assertEquals("<?php echo substr(md5(\$name ),1,2); ?>"
            , $this->blade->compileString('{!! $name | md5 | substr:1,2 !!}',["name" => "12345"]));
        $this->assertEquals("<?php echo substr(substr(md5(\$name ),1,2 ),1,2); ?>"
            , $this->blade->compileString('{!! $name | md5 | substr:1,2 | substr:1,2  !!}',["name" => "12345"]));
        $this->assertEquals("<?php echo md5(\$name ); ?>"
            , $this->blade->compileString('{!! $name | md5 !!}', ["name" => "12345"]));
    }


    /**
     * @throws Exception
     */
    public function testDontPrintVariable(): void
    {
        $bladeString = /** @lang Blade */
            <<<'BLADE'
@{{ $var }}
BLADE;
        $this->assertEqualsIgnoringWhitespace("{{ \$var }}", $this->blade->runString($bladeString, ["var" => "my_var"]));
    }

    /**
     * @throws Exception
     */
    public function testVerbatim(): void
    {
        $bladeString = /** @lang Blade */
            <<<'BLADE'
@verbatim
{{ $var }}
@endverbatim
BLADE;
        $this->assertEqualsIgnoringWhitespace("{{ \$var }}", $this->blade->runString($bladeString, ["var" => "my_var"]));
    }

    public function testDefaultFallback() : void {

        $this->assertEquals('<?php echo \htmlentities(isset($_Name1) ? $_Name1 : \'Default\'??\'\', ENT_QUOTES, \'UTF-8\', false); ?>', $this->blade->compileString('{{ $_Name1 or \'Default\' }}'));
        $this->assertEquals('<?php echo isset($_Name1) ? $_Name1 : \'Default\'; ?>', $this->blade->compileString('{!! $_Name1 or \'Default\' !!}'));
        $this->assertEquals('<?php echo isset($_Name1) ? $_Name1 : "Default"; ?>', $this->blade->compileString('{!! $_Name1 or "Default" !!}'));
        $this->assertEquals('<?php echo isset($_Name1) ? $_Name1 : fallback("test2"); ?>', $this->blade->compileString('{!! $_Name1 or fallback("test2") !!}'));
        $this->assertEquals('<?php echo isset($_Name1) ? $_Name1 : fallback(\'test2\'); ?>', $this->blade->compileString('{!! $_Name1 or fallback(\'test2\') !!}'));
    }
    
    public function testNotDefaultFallback() : void
    {
        $this->assertEquals('<?php echo \htmlentities(T->method(\'TEST or TEST2\')??\'\', ENT_QUOTES, \'UTF-8\', false); ?>', $this->blade->compileString('{{ T->method(\'TEST or TEST2\') }}'));
        $this->assertEquals('<?php echo function1("TEST or TEST2"); ?>', $this->blade->compileString('{!! function1("TEST or TEST2") !!}'));
        $this->assertEquals('<?php echo $t("TEST or TEST2"); ?>', $this->blade->compileString('{!! $t("TEST or TEST2") !!}'));
        $this->assertEquals('<?php echo $t->method("TEST or TEST2"); ?>', $this->blade->compileString('{!! $t->method("TEST or TEST2") !!}'));
        $this->assertEquals('<?php echo \htmlentities($t->method("TEST or TEST2")??\'\', ENT_QUOTES, \'UTF-8\', false); ?>', $this->blade->compileString('{{ $t->method("TEST or TEST2") }}'));
        $this->assertEquals('<?php echo \htmlentities($t->method(\'TEST or TEST2\')??\'\', ENT_QUOTES, \'UTF-8\', false); ?>', $this->blade->compileString('{{ $t->method(\'TEST or TEST2\') }}'));
        $this->assertEquals('<?php echo \htmlentities(T::staticMethod(\'TEST or TEST2\')??\'\', ENT_QUOTES, \'UTF-8\', false); ?>', $this->blade->compileString('{{ T::staticMethod(\'TEST or TEST2\') }}'));
        $this->assertEquals('<?php echo \htmlentities(T::staticMethod(\'TEST or TEST2\')??\'\', ENT_QUOTES, \'UTF-8\', false); ?>', $this->blade->compileString('{{ T::staticMethod(\'TEST or TEST2\') }}'));
        $this->assertEquals('<?php echo \htmlentities($t::staticMethod(\'TEST or TEST2\')??\'\', ENT_QUOTES, \'UTF-8\', false); ?>', $this->blade->compileString('{{ $t::staticMethod(\'TEST or TEST2\') }}'));
        $this->assertEquals('<?php echo \htmlentities($t::staticMethod(\'TEST or TEST2\')??\'\', ENT_QUOTES, \'UTF-8\', false); ?>', $this->blade->compileString('{{ $t::staticMethod(\'TEST or TEST2\') }}'));
    }
}
