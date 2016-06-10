<?php
/**
 * Copyright (c) 2016 Jorge Patricio Castro Castillo MIT License.
 */
include "../BladeOne.php";

include "../BladeOneHtml.php";
include "../BladeOneLogic.php";
use eftec\bladeone;

$views = __DIR__ . '/views';
$cache = __DIR__ . '/cache';
$blade=new bladeone\BladeOneLogic($views,$cache);

//<editor-fold desc="Example data">
$countries=array();
$country=new stdClass();
        $country->id=1;
        $country->name="Argentina";
$countries[]=$country;
$country=new stdClass();
        $country->id=2;
        $country->name="Canada";
$countries[]=$country;
$country=new stdClass();
        $country->id=3;
        $country->name="United States";
$countries[]=$country;
        $country=new stdClass();
        $country->id=4;
        $country->name="Japan";
$countries[]=$country;

$countrySelected=3;
//</editor-fold>

define("BLADEONE_MODE",1); // (optional) 1=forced (test),2=run fast (production), 0=automatic, default value.
echo $blade->run("helloextensions"
    ,["countries"=>$countries,'countrySelected'=>$countrySelected]);