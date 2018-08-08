<?php
/**
 * Copyright (c) 2016 Jorge Patricio Castro Castillo MIT License.
 */
include "../lib/BladeOne.php";
use eftec\bladeone\BladeOne;

$views = __DIR__ . '/views';
$compiledFolder = __DIR__ . '/compiled';
$blade=new BladeOne($views,$compiledFolder,BladeOne::MODE_SLOW);



//<editor-fold desc="Authentication example">


// This new lines are optional. Blade has build in validations by default. However, you can personaliza your own authentication.
$blade->setCanFunction(function($action,$subject=null) {
    global $blade;
    if ($subject=='noallowed') return false;
    return in_array($action,$blade->currentPermission);
});

$blade->setAnyFunction(function($array) {
    global $blade;
    foreach($array as $permission) {
        if (in_array($permission,$blade->currentPermission)) return true;
    }
    return false;
});

//</editor-fold>


$blade->setAuth("john","admin",['edit','delete']);
try {
    echo $blade->run("Test2.auth", ['title'=>'Testing an user that is administrator. He could edit,delete and has the role of administrator']);
} catch (Exception $e) {
    echo "error found ".$e->getMessage()."<br>".$e->getTraceAsString();
}

$blade->setAuth("mary","user",['view']);
try {
    echo $blade->run("Test2.auth", ['title'=>'Testing an user that is a normal user. She could only view and has the role of user']);
} catch (Exception $e) {
    echo "error found ".$e->getMessage()."<br>".$e->getTraceAsString();
}

$blade->setAuth(null);
try {
    echo $blade->run("Test2.auth", ['title'=>'Testing an user that is anonymous.']);
} catch (Exception $e) {
    echo "error found ".$e->getMessage()."<br>".$e->getTraceAsString();
}