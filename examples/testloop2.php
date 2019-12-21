<?php
/**
 * Copyright (c) 2016 Jorge Patricio Castro Castillo MIT License.
 */
include "../lib/BladeOne.php";
use eftec\bladeone\BladeOne;

$views = __DIR__ . '/views';
$compiledFolder = __DIR__ . '/compiled';
$blade=new BladeOne($views,$compiledFolder,BladeOne::MODE_DEBUG);


$users=[];

$john=new stdClass();
$john->name="John";
$john->post=[];

$annah=new stdClass();
$annah->name="Annah";
$annah->post=[];

$post1=new stdClass();
$post1->subject="Hi there 1";

$post2=new stdClass();
$post2->subject="Hi there 2";

$john->posts[]=$post1;
$john->posts[]=$post2;

$annah->posts[]=$post1;
$annah->posts[]=$post2;

$users[]=$john;
$users[]=$annah;


try {
    echo $blade->run("Test.loop2"
        , ["users" => $users]);
} catch (Exception $e) {
    echo "error found ".$e->getMessage()."<br>".$e->getTraceAsString();
}
