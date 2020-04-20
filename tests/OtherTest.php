<?php

namespace eftec\tests;

class OtherTest extends AbstractBladeTestCase
{
   
    /**
     * @throws \Exception
     */
    public function testJSON()
    {
        $drinks=['Coca','Fanta','Sprite'];
        $bladeSource = '@json($drinks)';
        $this->assertEquals('["Coca","Fanta","Sprite"]', $this->blade->runString($bladeSource, ["drinks"=>$drinks]));
        
        $bladeSource = '@json($drinks,JSON_FORCE_OBJECT)';
        $this->assertEqualsIgnoringWhitespace('{"0":"Coca","1":"Fanta","2":"Sprite"}', $this->blade->runString($bladeSource, ["drinks"=>$drinks]));
    }
    public function testwrapPHP()
    {
        $this->assertEquals('"aaaa"', $this->blade->wrapPHP('"aaaa"', '"', true));
        $this->assertEquals('"<?php echo "aaaa$bbb";?>"', $this->blade->wrapPHP('"aaaa$bbb"', '"', false));
    }
    public function test2()
    {
        $arr=$this->blade->parseArgs('a1=1 a2="2" a3=\'3\' a4=$aaa a5=function() a6=\'aaa bbb\'', ' ');
        $compare=['a1'=>'1','a2'=>'"2"','a3'=>"'3'",'a4'=>'$aaa','a5'=>'function()','a6'=>"'aaa bbb'"];


        $this->assertEquals($compare, $arr);
    }
}
