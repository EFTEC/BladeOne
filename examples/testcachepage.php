<?php /** @noinspection AutoloadingIssuesInspection */
/**
 * Copyright (c) 2016 Jorge Patricio Castro Castillo MIT License.
 */
include '../lib/BladeOne.php';

include '../lib/BladeOneCache.php';

use eftec\bladeone\BladeOne;
use eftec\bladeone\BladeOneCache;


class myBladeCache extends BladeOne
{
    use BladeOneCache;
}

$blade=new myBladeCache();
$blade->setCacheLog('cachelog.log');
$blade->setCacheStrategy('getpost',['id']);

define('BLADEONE_MODE', 0); // (optional) 1=forced (test),2=run fast (production), 0=automatic, default value.

if ($blade->cachePageExpired('TestCache.hellocache2', 5)) {
    echo '<b>Logic layer</b>: cache expired, re-reading the list<br>';
    $list = [1, 2, 3, 4, 5];
} else {
    echo "<b>Logic layer</b>: cache active, i don't read the list<br>";
    $list=[];
}


$random=mt_rand(0, 9999);
$time=date('h:i:s A', time());
$timeUpTo=date('h:i:s A', time()+5); // plus 5 seconds
//</editor-fold>


try {
    echo $blade->runCache('TestCache.hellocache2', ['random' => $random
            , 'time' => $time
            , 'list' => $list
            , 'timeUpTo' => $timeUpTo
            ], 5);
} catch (Exception $e) {
    echo 'error found ' .$e->getMessage(). '<br>' .$e->getTraceAsString();
}
