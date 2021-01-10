<?php
/**
 * Copyright (c) 2019 Jorge Patricio Castro Castillo MIT License.
 */

use eftec\bladeone\BladeOne;

include "../lib/BladeOne.php";
$blade=new BladeOne(null, null, BladeOne::MODE_DEBUG);
$blade->pipeEnable=true;
$blade->includeScope=false;
echo "<h1>Running with include scope=false</h1>";
/** examples/views/Test/include.blade.php */
echo $blade->run("Test.include2", ["g1" => "GLOBAL VARIABLE G1","g2"=>"GLOBAL VARIABLE G2"]);

$blade->includeScope=true;
echo "<h1>Running with include scope=true</h1>";
/** examples/views/Test/include.blade.php */
echo $blade->run("Test.include2", ["g1" => "GLOBAL VARIABLE G1","g2"=>"GLOBAL VARIABLE G2"]);

