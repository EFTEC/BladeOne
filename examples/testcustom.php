<?php
/**
 * Copyright (c) 2016 Jorge Patricio Castro Castillo MIT License.
 */
include "../lib/BladeOne.php";

include "../lib/BladeOneHtml.php";

include "../lib/BladeOneCustom.php";
use eftec\bladeone\BladeOne;

$views = __DIR__ . '/views';
$compiledFolder = __DIR__ . '/compiled';

class myBlade extends  bladeone\BladeOne {
    use bladeone\BladeOneHtml,bladeone\BladeOneCustom;
}

$blade=new myBlade($views,$compiledFolder);


//</editor-fold>
try {
    echo $blade->run("TestCustom.test"
        , []);
} catch (Exception $e) {
    echo "error found ".$e->getMessage()."<br>".$e->getTraceAsString();
}
