<?php
/**
 * Copyright (c) 2016 Jorge Patricio Castro Castillo MIT License.
 */
include "../BladeOne.php";

include "../BladeOneHtml.php";
include "../BladeOneLogic.php";
include "../BladeOneCustom.php";
use eftec\bladeone;

$views = __DIR__ . '/views';
$compiledFolder = __DIR__ . '/compiled';

class myBlade extends  bladeone\BladeOne {
    use bladeone\BladeOneLogic,bladeone\BladeOneHtml,bladeone\BladeOneCustom;
}

$blade=new myBlade($views,$compiledFolder);
define("BLADEONE_MODE",1); // (optional) 1=forced (test),2=run fast (production), 0=automatic, default value.

//</editor-fold>
echo $blade->run("TestCustom.test"
    ,[]);
