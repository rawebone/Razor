<?php

/**
 * Razor Framework Bootstrap
 */

require_once __DIR__ . "/vendor/autoload.php";
require_once __DIR__ . "/src/dsl.php";

use Razor\DSLAccessor;
use Razor\Application;
use Razor\ServiceResolver;
use Razor\HttpDispatcher;
use Symfony\Component\HttpFoundation\Request;

$boot = function ()
{
    // Isolate instances from the global scope
    $resolver = new ServiceResolver();
    injector()->resolver($resolver);

    $http = new HttpDispatcher(injector(), $resolver);

    // Initialise the application and push it onto the DSL Layer
    DSLAccessor::init(new Application($http));

    // Define our core services
    $resolver->registerService("request", function () { return Request::createFromGlobals(); });
};

$boot();
unset($boot);

