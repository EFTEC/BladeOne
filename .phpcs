<?php

$finder = PhpCsFixer\Finder::create()
    ->exclude('vendor')
    ->in(__DIR__);

return PhpCsFixer\Config::create()
	->setRiskyAllowed(true)
    ->setRules([
        'native_function_invocation' => true,
    ])
    ->setFinder($finder);