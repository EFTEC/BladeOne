<?php
/**
 * Copyright (c) 2016 Jorge Patricio Castro Castillo MIT License.
 */
include "../src/BladeOne.php";

include "../src/BladeOneHtml.php";
include "../src/BladeOneLogic.php";
include "../src/BladeOneCustom.php";
use eftec\bladeone;

$views = __DIR__ . '/views';
$compiledFolder = __DIR__ . '/compiled';

class myBlade extends  bladeone\BladeOne {
    use bladeone\BladeOneLogic,bladeone\BladeOneHtml,bladeone\BladeOneCustom;
}

$blade=new myBlade($views,$compiledFolder);
define("BLADEONE_MODE",1); // (optional) 1=forced (test),2=run fast (production), 0=automatic, default value.

//</editor-fold>
try {
    echo $blade->run("TestCustom.test"
        , []);
} catch (Exception $e) {
    echo "error found ".$e->getMessage()."<br>".$e->getTraceAsString();
}
