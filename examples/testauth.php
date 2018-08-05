<?php
/**
 * Copyright (c) 2016 Jorge Patricio Castro Castillo MIT License.
 */
include "../lib/BladeOne.php";
use eftec\bladeone;

$views = __DIR__ . '/views';
$compiledFolder = __DIR__ . '/compiled';
$blade=new bladeone\BladeOne($views,$compiledFolder);
define("BLADEONE_MODE",1); // (optional) 1=forced (test),2=run fast (production), 0=automatic, default value.


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