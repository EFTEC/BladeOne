<?php
/**
 * Copyright (c) 2016 Jorge Patricio Castro Castillo MIT License.
 */
include "../lib/BladeOne.php";
use eftec\bladeone\BladeOne;

$views = __DIR__ . '/views';
$compiledFolder = __DIR__ . '/compiled';
$blade=new BladeOne($views,$compiledFolder,BladeOne::MODE_SLOW);



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

$drinks7=array('Cocacola','Pepsi','Fanta','Sprite','7up',"Mountain Dew","Dr Pepper");
$drinks8=array('Cocacola','Pepsi','Fanta','Sprite','7up',"Mountain Dew","Dr Pepper",'Bilz&Pap');
//</editor-fold>

class ClassService {
    public static function myfunction() {
        return "hello world";
    }
}


try {
    echo $blade->run("Test.loop"
        , ["name" => "hola mundo"
            , 'records' => $records
            , 'users' => $users
            , 'drinks7' => $drinks7
            , 'drinks8' => $drinks8]);
} catch (Exception $e) {
    echo "error found ".$e->getMessage()."<br>".$e->getTraceAsString();
}
