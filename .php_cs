<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . '/lib');

return PhpCsFixer\Config::create()
	->setRiskyAllowed(true)
    ->setRules([
        'native_function_invocation' => true,
    ])
    ->setFinder($finder);