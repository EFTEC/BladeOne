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

$blade->addMethod('runtime','table',static function($args) {
    // in this context, $this means the autoTest class and not the blade class.
    $args=array_merge(['alias'=>'alias'],$args); // you could use array merge to set a default value.
    BladeOne::$instance->addControlStackChild('runtimeTable',$args); // we store the current control in the stack.
    return '<ul>';
});
$blade->addMethod('runtime','endtable',static function($args) {
    BladeOne::$instance->closeControlStack();
    return '</ul>';
});
$blade->addMethod('runtime','row',static function() {
    $parent=BladeOne::$instance->lastControlStack()['args']; // getting the values of the parent control using the stack
    $result='';
    foreach($parent['values'] as $v) {
        $result.=BladeOne::$instance->runChild('auto.test2_control',[$parent['alias']=>$v]);
    }
    return $result;
});
$blade->addMethod('runtime','row2',function() {
    $parent=BladeOne::$instance->lastControlStack()['args']; // getting the values of the parent control using the stack
    $result='';
    foreach($parent['values'] as $v) {
        $result.="<li>$v</li>\n";
    }
    return $result;
});


try {
    echo $blade->run("auto.test2",['countries' => ["chile","argentina","peru"]]);
} catch (Exception $e) {
    echo "error found ".$e->getMessage()."<br>".$e->getTraceAsString();
}
