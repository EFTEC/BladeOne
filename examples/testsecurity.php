<?php
namespace App;

include "../lib/BladeOne.php";

use eftec\bladeone\BladeOne;


$views = __DIR__ . '/views';
$compiledFolder = __DIR__ . '/compiled';
$blade=new BladeOne($views,$compiledFolder,BladeOne::MODE_SLOW);


$records=array(1,2,3);

$blade->login('johndoe','admin2');


class Post {

}


try {


    echo $blade->run('TestSecurity.test',array());

} catch (Exception $e) {
    echo $e->getMessage();
}