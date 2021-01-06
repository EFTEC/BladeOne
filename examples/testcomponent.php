<?php
/**
 * Copyright (c) 2017 Jorge Patricio Castro Castillo MIT License.
 */
include "../lib/BladeOne.php";
use eftec\bladeone\BladeOne;

$views = __DIR__ . '/views';
$compiledFolder = __DIR__ . '/compiled';
$blade=new BladeOne($views, $compiledFolder, BladeOne::MODE_DEBUG);
$blade->pipeEnable=true;


try {
    echo $blade->run("TestComponent.component", ['myglobal'=>'hello']);
} catch (Exception $e) {
    echo "error found ".$e->getMessage()."<br>".$e->getTraceAsString();
}
