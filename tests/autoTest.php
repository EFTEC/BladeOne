<?php

namespace eftec\tests;

use eftec\bladeone\BladeOne;

function sum(...$args)
{
    return array_sum($args);
}

class autoTest extends AbstractBladeTestCase
{
    /**
     * @throws \Exception
     */
    public function test1()
    {
        BladeOne::$instance->clearMethods();
        BladeOne::$instance->addMethod('runtime', 'one', static function($args) {
            return "method one " . $args['a1'] . ',' . $args['a2'];
        });
        BladeOne::$instance->addMethod('compile', 'two', function($args) {
            return BladeOne::$instance->wrapPHP("eftec\\tests\\sum$args", false, false);
        });
        $this->assertEquals("it is test 1\nmethod one hola,mundo\ntwo:6",
            BladeOne::$instance->run("auto.test1", ['a1' => 10, 'a2' => 20]));
    }

    public function test2()
    {
        // it clears the previous methods created in different tests
        BladeOne::$instance->clearMethods();
        BladeOne::$instance->addMethod('runtime', 'table', function($args) {
            // you could use array merge to set a default value, or use conditions, ternary operators, etc.
            $args = array_merge(['alias' => 'alias'], $args);
            // we store the current control in the stack, and we turn @table as the current parent
            BladeOne::$instance->addControlStackChild('table', $args);
            return '<ul>';
        });
        BladeOne::$instance->addMethod('runtime', 'endtable', function($args) {
            // it gets the last control, parent or child
            $latest=BladeOne::$instance->lastControlStack();
            // optionally you can add a validator and validate if the previous tag is the correnct
            if($latest['name']!=='table') {
                // it shows an error and throw an exception
                BladeOne::$instance->showError('@endtable', 'Missing @table',true,true);
            }
            BladeOne::$instance->closeControlStackParent(); // it closes the parent (@table)
            return '</ul>';
        });
        BladeOne::$instance->addMethod('runtime', 'row', function() {
            // getting the values of the parent control (@table) using the stack
            // note: we don't need to add a child everytime a new control is added, its optional
            $parent = BladeOne::$instance->parentControlStack()['args'];
            $result = '';
            foreach ($parent['values'] as $v) {
                $result .= BladeOne::$instance->runChild('auto.test2_control', [$parent['alias'] => $v]);
            }
            return $result;
        });
        BladeOne::$instance->addMethod('runtime', 'row2', function() {
            // getting the values of the parent control (@table) using the stack
            // note: we don't need to add a child everytime a new control is added, its optional
            $parent = BladeOne::$instance->parentControlStack()['args'];
            $result = '';
            foreach ($parent['values'] as $v) {
                $result .= "<li>$v</li>\n";
            }
            return $result;
        });
        $this->assertEquals("<ul>" .
            "<li>chile</li>\n<li>argentina</li>\n<li>peru</li>\n" .
            "<li>chile</li>\n<li>argentina</li>\n<li>peru</li>\n" .
            "</ul>",
            BladeOne::$instance->run("auto.test2", ['countries' => ["chile", "argentina", "peru"]]));
    }
    /**
     * @throws \Exception
     */
}
