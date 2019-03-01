<?php

namespace eftec\tests;


class OtherTest extends AbstractBladeTestCase {
   
    /**
     * @throws \Exception
     */
    public function testJSON() {
    	$drinks=['Coca','Fanta','Sprite'];
        $bladeSource = '@json($drinks)';
        $this->assertEquals('["Coca","Fanta","Sprite"]', $this->blade->runString($bladeSource, ["drinks"=>$drinks]));
        
	    $bladeSource = '@json($drinks,JSON_FORCE_OBJECT)';
	    $this->assertEqualsIgnoringWhitespace('{"0":"Coca","1":"Fanta","2":"Sprite"}', $this->blade->runString($bladeSource, ["drinks"=>$drinks]));
        
    }

}
