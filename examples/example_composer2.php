<?php

/**
 * Copyright (c) 2020 Jorge Patricio Castro Castillo MIT License.
 */

use eftec\bladeone\BladeOne;

include __DIR__."/../lib/BladeOne.php";


$bladeOne=new BladeOne(__DIR__.'/views', __DIR__.'/compiles',BLADEONE::MODE_DEBUG);

$bladeOne->composer('exampleextends.layout', function ($view) {
    $view->with([
        'header' => "IT IS THE HEADER",
        'footer' => "IT IS THE FOOTER",
    ]);
});
$bladeOne->composer('exampleextends.example2', function ($view) {
    $view->with([
        'content' => "IT IS THE CONTENT"
    ]);
});




echo $bladeOne->run('exampleextends.example2');
