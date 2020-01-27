<?php

namespace {
/**
 * Copyright (c) 2016 Jorge Patricio Castro Castillo MIT License.
 */
include "../lib/BladeOne.php";
include "../lib/BladeOneHtml.php";
include "../lib/BladeOneCustom.php";



use eftec\bladeone\BladeOne;
use eftec\bladeone\BladeOneCustom;
use eftec\bladeone\BladeOneHtml;

$views = __DIR__ . '/views';
    $compiledFolder = __DIR__ . '/compiled';

    class myBlade extends BladeOne
    {
        protected function compileMyFunction($expression = '')
        {
            return $this->phpTag . "echo 'YAY MY FUNCTION IS WORKING " . $expression . "'; ?>";
        }
    }

    $blade = new myBlade($views, $compiledFolder);
    $blade->addAliasClasses('SomeClass', '\mynamespace\SomeClass');
    $blade->setMode(BladeOne::MODE_DEBUG);

//</editor-fold>
    try {

        echo $blade->run("TestCustom.test2", []);
    } catch (Exception $e) {
        echo "error found " . $e->getMessage() . "<br>" . $e->getTraceAsString();
    }

}


namespace mynamespace {

    class SomeClass
    {
        public static function method($arg='empty')
        {
            return "<b>Method SomeClass::method($arg) called</b>";
        }
    }
}
