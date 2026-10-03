<?php

include '../lib/BladeOne.php';
$p=@parse_url('/aaa')['host'] or '';
var_dump($p);

$b=new \eftec\bladeone\BladeOne();
$b->setBaseUrl('http://localhost/');
var_dump($b->getBaseUrl());
var_dump($b->getRelativePath());
var_dump($b->getCanonicalUrl());
var_dump($b->getCurrentUrl());

var_dump($b->getCurrentUrlCalculated());