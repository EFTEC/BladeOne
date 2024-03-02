<?php
/**
 * Copyright (c) 2024 Jorge Patricio Castro Castillo MIT License.
 */
include "../lib/BladeOne.php";
use eftec\bladeone\BladeOne;

$views = __DIR__ . '/views';
$compiledFolder = __DIR__ . '/compiled';
$blade=new BladeOne($views, $compiledFolder, BladeOne::MODE_DEBUG);
$blade->pipeEnable=true;


$blade->clearMethods();


$blade->addMethod('runtime','card',static function($args) { // @card($item)
    $result='';
    $result.=BladeOne::$instance->runChild('auto.card',['value'=>$args[0]]); // auto.card is a view.
    return $result;
});


try {
    echo $blade->run("auto.test3",['items' => [
        ['title'=>"chile",'content'=>'lorem ipsum'],
        ['title'=>"argentina",'content'=>'lorem ipsum'],
        ['title'=>"peru",'content'=>'lorem ipsum'],
    ]]);
} catch (Exception $e) {
    echo "error found ".$e->getMessage()."<br>".$e->getTraceAsString();
}
