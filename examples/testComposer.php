<?php
include "../vendor/autoload.php";

echo "this example uses composer's autocomplete<br>";

use eftec\bladeone\BladeOne;

$views = __DIR__ . '/views';
$compiledFolder = __DIR__ . '/compiled';

$blade=new BladeOne($views,$compiledFolder,BladeOne::MODE_SLOW);


echo "ok<br>";