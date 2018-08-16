<?php
use eftec\bladeone\BladeOne;

include "../lib/BladeOne.php";


$views = __DIR__ . '/views';
$compiledFolder = __DIR__ . '/compiled';
$blade=new BladeOne($views,$compiledFolder,BladeOne::MODE_DEBUG);

$blade->if('isnegative', function ($value) {
    return ($value<0);
});

$blade->if('isequals', function ($value,$value2) {
    return ($value==$value2);
});

try {
    echo $blade->run("Test3.customif", array('i' => 5,'e' => -5));
} catch (Exception $e) {
}