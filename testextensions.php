<?php
/**
 * Copyright (c) 2016 Jorge Patricio Castro Castillo MIT License.
 */
include "BladeOne.php";
include "BladeOneHtml.php";
include "BladeOneLogic.php";

$views = __DIR__ . '/views';
$cache = __DIR__ . '/cache';
$blade=new BladeOneLogic($views,$cache);

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
echo $blade->run("helloextensions"
    ,["countries"=>$countries,'countrySelected'=>$countrySelected]
    ,true);