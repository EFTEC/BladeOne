<?php

namespace App\Services {

	class SimpleClass {
		private $varName;
		function __construct($varName) {
			$this->varName = $varName;
		}

		function ping($pong) {
			return "[$this->varName] $pong";
		}
	}

}

namespace {
	include "../lib/BladeOne.php";

	use eftec\bladeone\BladeOne;

	$views = __DIR__ . '/views';
	$compiledFolder = __DIR__ . '/compiled';
	$blade = new BladeOne($views, $compiledFolder, BladeOne::MODE_SLOW);
	$blade->setInjectResolver(function ($className, $varName) {
		$fullClassName = "App\\Services\\$className";
		return new $fullClassName($varName);
	});


	$records = array(1, 2, 3);

	include "service/Metric.php";


	try {
		echo $blade->run("Test.inject2"
			, ["name" => "hello"
				, 'records' => $records
				, 'emptyArray' => array()
			]);

	} catch (Exception $e) {
		echo $e->getMessage();
	}

}