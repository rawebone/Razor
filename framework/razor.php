<?php

/**
 * Razor Framework Bootstrap
 */

require_once __DIR__ . "/vendor/autoload.php";
require_once __DIR__ . "razor.php/"

use Razor\Application;
use Razor\ServiceResolver;
use Razor\HttpDispatcher;
use Razor\TestRecorder;
use Razor\HttpTester;


$boot = function ()
{
    $resolver = new ServiceResolver();
    injector()->resolver($resolver);

    $http = new HttpDispatcher(injector(), $resolver);
    $tester = new HttpTester(injector(), $resolver, new TestRecorder());

    // Installs itself globally via an internal global
    new Application(injector(), $resolver);
};

$boot();
