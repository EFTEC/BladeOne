<?php
namespace eftec\tests;

use Cassandra\Date;

/**
 * @author G.J.W. Oolbekkink <g.j.w.oolbekkink@gmail.com>
 * @since 16/09/2018
 */
class PipeTest extends AbstractBladeTestCase
{
    /**
     * @throws \Exception
     */
    public function testPipe()
    {
        $this->blade->pipeEnable=true;
        $bladeSource = '{{$name | strtolower| strtolower | strtolower}}';
        
        $this->assertEqualsIgnoringWhitespace("john", $this->blade->runString($bladeSource, ['name' => 'john']));
        $bladeSource = '{{$name | ucfirst}}';
        
        $this->assertEqualsIgnoringWhitespace("John", $this->blade->runString($bladeSource, ['name' => 'john']));
        $bladeSource = '{{$name | substr:0,5}}';
        $this->assertEqualsIgnoringWhitespace("Jack", $this->blade->runString($bladeSource, ['name' => 'Jack Sparrow']));
        
        $bladeSource = '{{$date | format:"y/m/d"}}';
        $this->assertEqualsIgnoringWhitespace("20/01/01", $this->blade->runString($bladeSource, ['date' => new \DateTime('2020-01-01')]));
        
        $this->blade->pipeEnable=false;
    }
}
