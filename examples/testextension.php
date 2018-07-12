<?php
/**
 * Copyright (c) 2016 Jorge Patricio Castro Castillo MIT License.
 */
include "../lib/BladeOne/BladeOne.php";

include "../lib/BladeOne/BladeOneHtml.php";

use eftec\bladeone;

$views = __DIR__ . '/views';
$compiledFolder = __DIR__ . '/compiled';

class myBlade extends  bladeone\BladeOne {
    use bladeone\BladeOneHtml;
}

$blade=new myBlade($views,$compiledFolder);
define("BLADEONE_MODE",1); // (optional) 1=forced (test),2=run fast (production), 0=automatic, default value.

//<editor-fold desc="Example data">
$countries=array();
$country=new stdClass();
        $country->id=1;
        $country->cod='ar';
        $country->name="Argentina";
        $country->continent="America";
$countries[]=$country;
$country=new stdClass();
        $country->id=2;
        $country->cod='ca';
        $country->name="Canada";
        $country->continent="America";
$countries[]=$country;
$country=new stdClass();
        $country->id=3;
        $country->cod='us';
        $country->name="United States";
        $country->continent="America";
$countries[]=$country;
        $country=new stdClass();
        $country->id=4;
        $country->cod='jp';
        $country->name="Japan";
        $country->continent="Asia";
$countries[]=$country;
$countrySelected=3;
$multipleSelect=[1,2];
//</editor-fold>
try {
    echo $blade->run("TestExtension.helloextensions"
        , ["countries" => $countries
            , 'countrySelected' => $countrySelected
            , 'multipleSelect' => $multipleSelect]);
} catch (Exception $e) {
    echo "error found ".$e->getMessage()."<br>".$e->getTraceAsString();
}
