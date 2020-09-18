<?php
/**
 * Copyright (c) 2016 Jorge Patricio Castro Castillo MIT License.
 */

use eftec\bladeone\BladeOne;

include "../lib/BladeOne.php";

$blade=new BladeOne(null, null, BladeOne::MODE_DEBUG);
$blade->pipeEnable=true;

// adding a new method
$methodOne = static function ($arg=null) {
    echo 'It is the method 1 '.$arg;
};
$blade->directive('method1', $methodOne);

function method2($arg=null) {
    return 'it is the method 2 '.$arg;
}



try {
    //echo $blade->run("Test/hello.blade.php" // also works
    echo $blade->run("Test.pipe", ["name" => "Jack Sparrow",'othername'=>'Popeye','date'=>new DateTime()]);
} catch (Exception $e) {
    echo $e->getMessage();
}
