<?php
/**
 * Copyright (c) 2016 Jorge Patricio Castro Castillo MIT License.
 */


use eftec\bladeone\BladeOne;

include "../lib/BladeOne.php";
echo "<hr>";

$blade=new BladeOne(null,null,BladeOne::MODE_DEBUG);
$blade->setOptimize(false); // the result is not optimized (it will not remove tabs of multiple spaces)
$blade->setIsCompiled(false); // the result is not compiled on a file.


try {
	echo $blade->run("Test.identation",[]);
} catch (Exception $e) {
	echo $e->getMessage();
}

