<?php
/**
 * Copyright (c) 2016 Jorge Patricio Castro Castillo MIT License.
 */

use eftec\bladeone\BladeOne;

include "../lib/BladeOne.php";

$blade=new BladeOne(['./views','./views2'], null, BladeOne::MODE_AUTO);


@rename('./views/compile/compile1.blade.php.bak', './views/compile/compile1.blade.php');


    echo $blade->run("compile.compile1", []);


rename('./views/compile/compile1.blade.php', './views/compile/compile1.blade.php.bak');

// we re-created the second compile to change the compilation time.
$content=file_get_contents('./views2/compile/compile1.blade.php');
file_put_contents('./views2/compile/compile1.blade.php',$content); 

echo $blade->run("compile.compile1", []);
/*
echo $blade->run("Test.hello"
    ,["name"=>"hola mundo"
        ,'records'=>$records
        ,'users'=>$users
        ,'drinks7'=>$drinks7
        ,'drinks8'=>$drinks8]);
*/
