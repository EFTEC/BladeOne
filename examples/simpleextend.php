<?php
/**
 * Copyright (c) 2016 Jorge Patricio Castro Castillo MIT License.
 */
include "../lib/BladeOne.php";
use eftec\bladeone\BladeOne;

$views = __DIR__ . '/views';
$compiledFolder = __DIR__ . '/compiled';
$blade=new BladeOne($views,$compiledFolder,BladeOne::MODE_DEBUG);



//<editor-fold desc="Example data">
$v1=1;

try {
    echo $blade->run("simpleextend.origin", ["content"=>"first"]);
} catch (Exception $e) {
    echo "error found ".$e->getMessage()."<br>".$e->getTraceAsString();
}

echo $blade->run("simpleextend.origin2", ["content"=>"second"]);