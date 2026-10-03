<?php
require_once('vendor/autoload.php');

use eftec\bladeone;
use eftec\bladeone\BladeOneLang;

class MyBladeLang extends bladeone\BladeOne
{
    use BladeOneLang;
}
$views = __DIR__ . '/views';
$compiledFolder = __DIR__ . '/compiled';


$blade = new \MyBladeLang($views, $compiledFolder, MyBladeLang::MODE_SLOW);

$lang='jp'; // try es,jp or fr
include __DIR__.'/lang/'.$lang.'.php';


try {
    echo $blade->run("Lang.test", array('var'=>'1'));
} catch (Exception $e) {
    echo "error found ".$e->getMessage()."<br>".$e->getTraceAsString();
}

phpinfo();
