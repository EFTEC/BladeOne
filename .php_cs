<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . '/lib');

return PhpCsFixer\Config::create()
	->setRiskyAllowed(true)
    ->setRules([
        '@PSR2' => true,
        'array_syntax' => ['syntax' => 'short'],
    ])
    ->setFinder($finder);