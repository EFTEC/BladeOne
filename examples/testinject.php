<?php
include "../lib/BladeOne.php";
use eftec\bladeone\BladeOne;

$views = __DIR__ . '/views';
$compiledFolder = __DIR__ . '/compiled';
$blade=new BladeOne($views,$compiledFolder,BladeOne::MODE_SLOW);


$records=array(1,2,3);

include "service/Metric.php";

class SimpleClass {
    function ping($pong) {
        return $pong;
    }
}





try {
    echo $blade->run("Test.inject"
        , ["name" => "hello"
            , 'records' => $records
            , 'emptyArray' => array()
        ]);

} catch (Exception $e) {
    echo $e->getMessage();
}