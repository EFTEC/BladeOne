<?php
use eftec\bladeone\BladeOne;

if (!isset($flag)) {
    die("you should call <a href='relative1/relative2/callrelative.php'>relative1/relative2/callrelative.php</a>");
}

include __DIR__."/../lib/BladeOne.php";


$views = __DIR__ . '/views';
$compiledFolder = __DIR__ . '/compiled';
$blade=new BladeOne($views,$compiledFolder,BladeOne::MODE_DEBUG);

// It is an example, usually baseurl is a fixed value
// However, in this example, we are faking a fixed value by obtaining a value and removing the relative path and parameters
$fullurl='http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
$fullurl=str_replace('relative1/relative2/callrelative.php','',$fullurl);
$arr=explode('?',$fullurl);
$fullurl=$arr[0];
$blade->setBaseUrl($fullurl);
$blade->addAssetDict('js/jquery.min.js','https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js');


echo $blade->run("relative.relative",['baseurl'=>$blade->getBaseUrl()]);