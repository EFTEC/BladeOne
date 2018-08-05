<?php
include "../vendor/autoload.php";


use eftec\bladeone\BladeOne;

$views = __DIR__ . '/views';
$compiledFolder = __DIR__ . '/compiled';

$blade=new BladeOne($views,$compiledFolder);


echo "ok";