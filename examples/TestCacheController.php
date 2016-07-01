<?php
/**
 * Copyright (c) 2016 Jorge Patricio Castro Castillo MIT License.
 */
include "../BladeOne.php";

include "../BladeOneCache.php";

use eftec\bladeone;

$views = __DIR__ . '/views';
$compiledFolder = __DIR__ . '/compiled';

class myBlade extends  bladeone\BladeOne {
    use bladeone\BladeOneCache;
}

$blade=new myBlade($views,$compiledFolder);
define("BLADEONE_MODE",0); // (optional) 1=forced (test),2=run fast (production), 0=automatic, default value.

//<editor-fold desc="Example data">
if ($blade->cacheExpired('TestCache.hellocache',1,5)) {
    echo "<b>Logic layer</b>: cache expired, re-reading the list<br>";
    $list = [1, 2, 3, 4, 5];
} else {
    echo "<b>Logic layer</b>: cache active, i don't read the list<br>";
    $list=[];
}

$random=rand(0,9999);
$time=date('h:i:s A',time());
$timeUpTo=date('h:i:s A',time()+5); // plus 5 seconds
//</editor-fold>


echo $blade->run("TestCache.hellocache"
    ,["random"=>$random
    ,'time'=>$time
    ,'list'=>$list
    ,'timeUpTo'=>$timeUpTo]);
