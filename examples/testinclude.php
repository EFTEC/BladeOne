<?php
/**
 * Copyright (c) 2019 Jorge Patricio Castro Castillo MIT License.
 */

use eftec\bladeone\BladeOne;

include "../lib/BladeOne.php";
$blade=new BladeOne(null, null, BladeOne::MODE_DEBUG);
/** examples/views/Shared/input.blade.php */
$blade->addInclude('Shared.input', 'input');
/** examples/views/Shared/input2.blade.php */
$blade->addInclude('Shared.input2');

$blade->share('globalme', 'it is a global variable');

/** examples/views/Test/include.blade.php */
echo $blade->run("Test.include", ["title" => "VARIABLE TITLE GOES HERE"]);
