<?php /** @noinspection PhpMissingParamTypeInspection */
/** @noinspection ReturnTypeCanBeDeclaredInspection */
/** @noinspection PhpUnused */

/** @noinspection PhpMissingReturnTypeInspection */

namespace eftec\tests;

use eftec\bladeone\BladeOne;

trait TraitExample
{
    public $fieldTrait;

    /**
     * We create the new tags @ hello <br>
     * The name of the method must starts with "compile"<br>
     * <b>Example:</b><br>
     * <pre>
     * @hello()
     * @hello("name")
     * </pre>
     *
     * @param null|string $expression expects a value like null, (), ("hello") or ($somevar)
     * @return string returns a fragment of code (php and html)
     */
    public function compileHello($expression=null)
    {
        if ($expression===null || $expression==='()') {
            return "<?php echo '--empty--'; ?>";
        }
        return "<?php echo 'Hello '.$expression; ?>";
    }

    /**
     * We create the new tags @ hellonamed <br>
     * The name of the method must starts with "compile"<br>
     * <b>Example:</b><br>
     * <pre>
     * @hellonamed()
     * @hellonamed(name="name")
     * </pre>
     *
     * @param null|string $expression expects a value like null, (), ("hello") or ($somevar)
     * @return string returns a fragment of code (php and html)
     */
    public function compileHelloNamed($expression)
    {
        $args = $this->getArgs($expression); // args separates the arguments by name
        $name= $args['name'] ?? '--empty--';
        return "<?php echo 'Hello '.$name; ?>";
    }

    /**
     * Constructor. It must has the name of the trait, it must be public and it must has zero arguments.
     * @noinspection PhpUnused
     */
    public function TraitExample()
    {
        $this->fieldTrait='loaded';
    }
}

class ClassUsingTraitExample extends BladeOne
{
    use TraitExample;
}

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

    /**
     * @throws \Exception
     */
    public function testTrait()
    {
        $blade=new ClassUsingTraitExample();
        $this->assertEquals('loaded', $blade->fieldTrait);
        $this->assertEquals('--empty--', $blade->runString('@hello()'));
        $this->assertEquals('Hello Corona-chan', $blade->runString('@hello("Corona-chan")'));
        $this->assertEquals('Hello Corona-chan', $blade->runString('@hello($name)', ['name'=>'Corona-chan']));
        $this->assertEquals('Hello Corona-chan', $blade->runString('@helloNamed(name="Corona-chan")'));
        $this->assertEquals('Hello Corona-chan', $blade->runString('@helloNamed(name=$name)', ['name'=>'Corona-chan']));
    }
    public function test2()
    {
        $arr=$this->blade->parseArgs('a1=1 a2="2" a3=\'3\' a4=$aaa a5=function() a6=\'aaa bbb\' a7', ' ');
        $compare=['a1'=>'1','a2'=>'"2"','a3'=>"'3'",'a4'=>'$aaa','a5'=>'function()','a6'=>"'aaa bbb'",'a7'=>null];
        $this->assertEquals($compare, $arr);
        
        $this->assertEquals(true, $this->blade->isQuoted("'aaa'"));
        $this->assertEquals(false, $this->blade->isQuoted("aaa'"));
        $this->assertEquals(true, $this->blade->isQuoted('"aaa"'));
        $this->assertEquals(false, $this->blade->isQuoted(''));
        $this->assertEquals(false, $this->blade->isQuoted('"'));
        $this->assertEquals(false, $this->blade->isQuoted(null));
        $this->assertEquals(false, $this->blade->isVariablePHP('aaa'));
        $this->assertEquals(true, $this->blade->isVariablePHP('$aaa'));
        $this->assertEquals(false, $this->blade->isVariablePHP(''));
        $this->assertEquals(false, $this->blade->isVariablePHP(null));
        $this->assertEquals(false, $this->blade->isVariablePHP('$'));
        $this->assertEquals('"aaabcd"', $this->blade->addInsideQuote('"aaa"', "bcd"));
        $this->assertEquals('aaa"bcd', $this->blade->addInsideQuote('aaa"', "bcd"));
        $this->assertEquals('bcd', $this->blade->addInsideQuote('', "bcd"));
        $this->assertEquals('"bcd"', $this->blade->addInsideQuote('""', "bcd"));

        $arr=$this->blade->parseArgs('a1=1 a2=function(1 2 3)', ' ');
        $compare=['a1'=>'1','a2'=>'function(1 2 3)'];
        $this->assertEquals($compare, $arr);
    }
}
