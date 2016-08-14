<?php
/**
 * Copyright (c) 2016 Jorge Patricio Castro Castillo MIT License.
 */
include "../BladeOne.php";
use eftec\bladeone;

$views = __DIR__ . '/views';
$compiledFolder = __DIR__ . '/compiled';
$blade=new bladeone\BladeOne($views,$compiledFolder);
define("BLADEONE_MODE",1); // (optional) 1=forced (test),2=run fast (production), 0=automatic, default value.


//<editor-fold desc="Example data">
$name="New User";
$records=array(1,2,3);
$users=array();
$usr=new stdClass();
    $usr->id=1;
    $usr->name="John Doe";
    $usr->type=1;
    $usr->number=1;
$users[]=$usr;
$usr=new stdClass();
    $usr->id=2;
    $usr->name="Anna Smith";
    $usr->type=2;
    $usr->number=5;
$users[]=$usr;
//</editor-fold>

class ClassService {
    public static function Function() {
        return "hello world";
    }
}

echo $blade->run("Test.hello2"
    ,["name"=>"hola mundo"
    ,'records'=>$records
    ,'users'=>$users]);


echo $blade->run("Test.hello"
    ,["name"=>"hola mundo"
        ,'records'=>$records
        ,'users'=>$users]);
