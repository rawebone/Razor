<?php

use Razor\DSLAccessor as Razor;

function get($fn)
{
    $trace = debug_backtrace();
    $controller = $trace[0]["file"];

    Razor::controller($controller)->get = $fn;
}

function post($fn)
{
    $trace = debug_backtrace();
    $controller = $trace[0]["file"];

    Razor::controller($controller)->post = $fn;
}

function run()
{
    $trace = debug_backtrace();
    $controller = $trace[0]["file"];

    Razor::run($controller);
}

