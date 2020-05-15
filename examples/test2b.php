<?php
/**
 * Copyright (c) 2016 Jorge Patricio Castro Castillo MIT License.
 */
include "../lib/BladeOne.php";
use eftec\bladeone\BladeOne;

$views = __DIR__ . '/views';
$compiledFolder = __DIR__ . '/compiled';
$blade=new BladeOne($views, $compiledFolder, BladeOne::MODE_DEBUG);

function myFunction()
{
    echo "<br>Running the function <b>myFunction()</b><br>";
}

//<editor-fold desc="Example data">
$v1=1;

try {
    /**
     * @see examples/views/Test/hello2.blade.php
     */
    echo $blade->run("Test.hello2", ['other' => 'hello world', 'v1' => $v1]);
} catch (Exception $e) {
    echo "error found ".$e->getMessage()."<br>".$e->getTraceAsString();
}
