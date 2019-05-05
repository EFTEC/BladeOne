<?php
/**
 * Copyright (c) 2019 Jorge Patricio Castro Castillo MIT License.
 */

use eftec\bladeone\BladeOne;

include "../lib/BladeOne.php";
$blade=new BladeOne(null,null, BladeOne::MODE_DEBUG);

$blade->share('globalvar','IT IS GLOBAL');

/** examples/views/Test/test.blade.php */
echo $blade->run("Test.share", ["localvar" => "IT IS LOCAL"]);

