<?php
/**
 * Copyright (c) 2018 Jorge Patricio Castro Castillo MIT License.
 */
@session_start();

include "../lib/BladeOne.php";
use eftec\bladeone\BladeOne;

$views = __DIR__ . '/views';
$compiledFolder = __DIR__ . '/compiled';
$blade=new BladeOne($views,$compiledFolder,BladeOne::MODE_SLOW);

$isvalid=$blade->csrfIsValid(); // a) for get= it generates a new token, b) for post, it validates the token.
session_write_close();// we close the session for writes.




//<editor-fold desc="Authentication example">

$field=@$_POST['field'];



//</editor-fold>


try {
    echo $blade->run("Test.token", ['field'=>$field,'isValid'=>$isvalid,'token'=>$blade->csrf_token]);
} catch (Exception $e) {
    echo "error found ".$e->getMessage()."<br>".$e->getTraceAsString();
}
