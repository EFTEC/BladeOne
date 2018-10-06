<?php
/**
 * Copyright (c) 2016 Jorge Patricio Castro Castillo MIT License.
 */

include "../lib/BladeOne.php";
include "../lib/BladeOneLang.php";
use eftec\bladeone\BladeOne;
use eftec\bladeone\BladeOneLang;

$views = __DIR__ . '/views';
$compiledFolder = __DIR__ . '/compiled';

class myBlade extends  BladeOne {
    use BladeOneLang;
}

$blade=new myBlade($views,$compiledFolder);



$blade->missingLog='c:\temp\missingkey.txt'; // if a traduction is missing the it will be saved here.

$lang='jp'; // try es,jp or fr
include __DIR__.'/lang/'.$lang.'.php';



//<editor-fold desc="Example data">


try {
    echo $blade->run("Lang.test",array('var'=>'1'));
} catch (Exception $e) {
    echo "error found ".$e->getMessage()."<br>".$e->getTraceAsString();
}
