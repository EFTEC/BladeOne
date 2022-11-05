<?php

namespace eftec;

// this code only runs on CLI but only if bladeonecli.php is called directly and via command line.
use eftec\bladeone\BladeOne;

if (!defined('PHPUNIT_COMPOSER_INSTALL') && !defined('__PHPUNIT_PHAR__')
    && isset($_SERVER['PHP_SELF']) &&
    !http_response_code() &&
    (basename($_SERVER['PHP_SELF']) === 'bladeonecli.php' || basename($_SERVER['PHP_SELF']) === 'bladeonecli')
) {
    // we also excluded it if it is called by phpunit.
    include_once __DIR__ . '/BladeOne.php';

    $compilepath = BladeOne::getParameterCli('compilepath', null);
    $templatepath = BladeOne::getParameterCli('templatepath', null);
    if (!BladeOne::isAbsolutePath($compilepath)) {
        $compilepath = getcwd() . '/' . $compilepath;
    }
    if (!BladeOne::isAbsolutePath($templatepath)) {
        $templatepath = getcwd() . '/' . $templatepath;
    }
    $inst = new BladeOne($templatepath, $compilepath);
    $inst->cliEngine();
} else {
    @http_response_code(404);
}

