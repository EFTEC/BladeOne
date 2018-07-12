<?php
/**
 * Copyright (c) 2016 Jorge Patricio Castro Castillo MIT License.
 */
include "../lib/BladeOne/BladeOne.php";
include "../lib/BladeOne/BladeOneLang.php";
use eftec\bladeone;
use eftec\bladeone\BladeOneLang;

$views = __DIR__ . '/views';
$compiledFolder = __DIR__ . '/compiled';

class myBlade extends  bladeone\BladeOne {
    use BladeOneLang;
}

$blade=new myBlade($views,$compiledFolder);
define("BLADEONE_MODE",1); // (optional) 1=forced (test),2=run fast (production), 0=automatic, default value.


$blade->missingLog='c:\temp\missingkey.txt'; // if a traduction is missing the it will be saved here.

$lang='jp'; // try es,jp or fr
include './lang/'.$lang.'.php';



//<editor-fold desc="Example data">


try {
    echo $blade->run("Lang.test");
} catch (Exception $e) {
    echo "error found ".$e->getMessage()."<br>".$e->getTraceAsString();
}
