<?php

/**
 * Razor Framework Bootstrap
 */

require_once(__DIR__ . "/vendor/autoload.php");
require_once(__DIR__ . "/src/dsl.php");
require_once(__DIR__ . "/../application/bootstrap.php");

use Razor\AppLog;
use Razor\Response;
use Razor\Filesystem;
use Razor\DSLAccessor;
use Razor\Application;
use Razor\ServiceResolver;
use Razor\HttpDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Debug\Debug;
use Symfony\Component\Debug\ErrorHandler;

$boot = function ()
{
    $applog =  new AppLog(__DIR__ . "/../application/app.log", new Filesystem());

    Debug::enable();
    ErrorHandler::setLogger($applog);

    // Isolate instances from the global scope
    $resolver = new ServiceResolver();
    injector()->resolver($resolver);

    $http = new HttpDispatcher(injector(), $resolver);

    // Initialise the application and push it onto the DSL Layer
    DSLAccessor::init(new Application($http, $resolver));

    // Define our core services
    $resolver->registerService("request", function ()
    {
        return Request::createFromGlobals();
    });

    $resolver->registerService("response", function ()
    {
        return new Response();
    });

    $resolver->registerService("applog", function () use ($applog)
    {
        return $applog;
    });
};

$boot();
unset($boot);

