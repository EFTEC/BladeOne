<?php
include "../lib/BladeOne.php";
use eftec\bladeone\BladeOne;

$views = __DIR__ . '/views';
$compiledFolder = __DIR__ . '/compiled';
$blade=new BladeOne($views,$compiledFolder,BladeOne::MODE_SLOW);


$records=array(1,2,3);


try {
    echo $blade->run("Test.test24"
        , ["name" => "hello"
            , 'records' => $records
            , 'emptyArray' => array()
        ]);

    echo $blade->run('Test.testappend',array());

} catch (Exception $e) {
    echo $e->getMessage();
}