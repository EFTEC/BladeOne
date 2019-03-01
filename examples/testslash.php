<?php
/**
 * Copyright (c) 2016 Jorge Patricio Castro Castillo MIT License.
 */


include "../lib/BladeOne.php";

$blade=new \eftec\bladeone\BladeOne(null,null,\eftec\bladeone\BladeOne::MODE_DEBUG);



try {
    echo $blade->run("Test.slash"
        , ["var" => "aaa/bbb\\aaa aa%2Fbbb%5Caaa"]);
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