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
use Razor\Templates\TestDescription;
use Razor\Templates\TestItem;
use Razor\Templates\TestResult;

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

    $http = new HttpDispatcher(injector(), $resolver);

    // Initialise the application and push it onto the DSL Layer
    DSLAccessor::init(new Application($http, $resolver));

    $resources = new ResourceManager($filesystem, __DIR__ . "/resources/");
    $renderer  = new TemplateRenderer();

    $testResultTpl = new TestResult($resources->framework("templates/test_results.html"), $renderer);
    $testDescTpl   = new TestDescription($resources->framework("templates/test_description.html"), $renderer);
    $testItemTpl   = new TestItem($resources->framework("templates/test_item.html"), $renderer);

    // Prepare the test system
    $listener = new HtmlTestListener(__DIR__ . "/../application/test.html", $filesystem, $testItemTpl, $testDescTpl, $testResultTpl);
    Jasmini::init(new Tester($listener, new Mocker(new Prophet(), new SignatureReader())));

    register_shutdown_function(function () use ($listener)
    {
        $listener->stop();
    });
};

$boot();
unset($boot);
