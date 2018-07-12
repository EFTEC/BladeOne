<?php
include "../lib/BladeOne/BladeOne.php";
use eftec\bladeone;

$views = __DIR__ . '/views';
$compiledFolder = __DIR__ . '/compiled';
$blade=new bladeone\BladeOne($views,$compiledFolder);
define("BLADEONE_MODE",1); // (optional) 1=forced (test),2=run fast (production), 0=automatic, default value.

$records=array(1,2,3);

$blade->login('johndoe','admin2');

try {


    echo $blade->run('TestSecurity.test',array());

} catch (Exception $e) {
    echo $e->getMessage();
}