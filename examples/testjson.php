<?php
/**
 * Copyright (c) 2016-2017 Jorge Patricio Castro Castillo MIT License.
 */
include "../lib/BladeOne/BladeOne.php";
use eftec\bladeone;

$views = __DIR__ . '/views';
$compiledFolder = __DIR__ . '/compiled';
$blade=new bladeone\BladeOne($views,$compiledFolder);
define("BLADEONE_MODE",1); // (optional) 1=forced (test),2=run fast (production), 0=automatic, default value.

$drinks= array('Cocacola','Pepsi','Fanta','Sprite','7up');
$json=json_encode($drinks);
header('Content-Type: application/json');
try {
    echo $blade->run("TestJson.example", ['json' => $json]);
} catch (Exception $e) {
    echo "error found ".$e->getMessage()."<br>".$e->getTraceAsString();
}

