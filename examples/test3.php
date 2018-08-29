<?php
include "../lib/BladeOne.php";
use eftec\bladeone\BladeOne;

$views = __DIR__ . '/views';
$compiledFolder = __DIR__ . '/compiled';
$blade=new BladeOne($views,$compiledFolder,BladeOne::MODE_SLOW);


$records=array(1
    ,2
    ,3
    ,array('b1'=>1,'b2'=>2));



try {
    echo $blade->run("Test.testdump"
        , ["name" => "hello"
            , 'records' => $records
            , 'emptyArray' => array()
        ]);



} catch (Exception $e) {
    echo $e->getMessage();
}