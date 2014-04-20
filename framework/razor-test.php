<?php

/**
 * Razor Test Framework Bootstrap
 */

require_once(__DIR__ . "/vendor/autoload.php");
require_once(__DIR__ . "/src/dsl.php");
require_once(__DIR__ . "/../application/bootstrap.php");
require_once(__DIR__ . "/vendor/rawebone/jasmini/library/dsl.php");

use Razor\AppLog;
use Razor\Filesystem;
use Razor\DSLAccessor;
use Razor\Application;
use Razor\ServiceResolver;
use Razor\HttpDispatcher;
use Razor\HtmlTestListener;
use Razor\ResourceManager;
use Razor\TemplateRenderer;

use Rawebone\Jasmini\DSLAccessor as Jasmini;
use Rawebone\Jasmini\Tester;
use Rawebone\Jasmini\Mocker;
use Rawebone\Injector\SignatureReader;
use Prophecy\Prophet;

use Symfony\Component\Debug\Debug;
use Symfony\Component\Debug\ErrorHandler;

$boot = function ()
{
    $filesystem = new Filesystem();
    $appLog = new AppLog(__DIR__ . "/../application/test.log", $filesystem);

    Debug::enable();
    ErrorHandler::setLogger($appLog);

    // Isolate instances from the global scope
    $resolver = new ServiceResolver();
    injector()->resolver($resolver);

    $resources = new ResourceManager($filesystem, __DIR__ . "/resources/");

    $http = new HttpDispatcher(injector(), $resolver);

    // Initialise the application and push it onto the DSL Layer
    DSLAccessor::init(new Application($http, $resolver));

    // Prepare the test system
    $listener = new HtmlTestListener(__DIR__ . "/../application/test.html", new Filesystem());
    Jasmini::init(new Tester($listener, new Mocker(new Prophet(), new SignatureReader())));
};

$boot();
unset($boot);
