<?php
/**
 * Copyright (c) 2016-2017 Jorge Patricio Castro Castillo MIT License.
 */
include "../lib/BladeOne.php";
use eftec\bladeone\BladeOne;

$views = __DIR__ . '/views';
$compiledFolder = __DIR__ . '/compiled';
$blade=new BladeOne($views,$compiledFolder,BladeOne::MODE_SLOW);


$drinks= array('Cocacola','Pepsi','Fanta','Sprite','7up');
$json=json_encode($drinks);
header('Content-Type: application/json');
try {
    echo $blade->run("TestJson.example", ['json' => $json]);
} catch (Exception $e) {
    echo "error found ".$e->getMessage()."<br>".$e->getTraceAsString();
}

