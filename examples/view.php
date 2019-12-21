<?php
/**
 * Copyright (c) 2016 Jorge Patricio Castro Castillo MIT License.
 */

use eftec\bladeone\BladeOne;

include "../lib/BladeOne.php";



//<editor-fold desc="Example data">
$name="New User";
$records=[1,2,3];
$users=[];
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

$drinks7=['Cocacola','Pepsi','Fanta','Sprite','7up'];
$drinks8=['Cocacola','Pepsi','Fanta','Sprite','7up','Bilz&Pap'];
//</editor-fold>


class View
{
    /** @var BladeOne */
    public static $blade;
    public static function buildMe()
    {
        self::$blade=new BladeOne(null, null, BladeOne::MODE_DEBUG);
    }
    
    public static function make($url) {
        return self::$blade->runChild($url);
    }
}



class ClassService
{
    public static function myfunction()
    {
        return "hello world";
    }
}

function asset($url='')
{
    return "hello world ".$url;
}

try {
    echo $blade->runString('<p>{{$direccion}}</p>', ['direccion' => 'Some address 20 #33-58']);
} catch (Exception $e) {
    echo "error found ".$e->getMessage()."<br>".$e->getTraceAsString();
}


try {
    //echo $blade->run("Test/hello.blade.php" // also works
    echo $blade->run("Test.hello", ["name" => "hola mundo"
            , 'records' => $records
            , 'users' => $users]);
} catch (Exception $e) {
    echo $e->getMessage();
}

/*
echo $blade->run("Test.hello"
    ,["name"=>"hola mundo"
        ,'records'=>$records
        ,'users'=>$users
        ,'drinks7'=>$drinks7
        ,'drinks8'=>$drinks8]);
*/
