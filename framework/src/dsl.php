<?php

/**
 * Razor - Domain Specific Language
 * ================================
 *
 * This file contains the syntactic sugar for working with the framework.
 */

use Razor\DSLAccessor as Razor;

/**
 * Registers the handler for a GET Request.
 *
 * @param \Closure $fn
 * @return void
 */
function get($fn)
{
    $trace = debug_backtrace();
    $controller = $trace[0]["file"];

    Razor::controller($controller)->get = $fn;
}

/**
 * Registers the handler for a POST Request.
 *
 * @param \Closure $fn
 * @return void
 */
function post($fn)
{
    $trace = debug_backtrace();
    $controller = $trace[0]["file"];

    Razor::controller($controller)->post = $fn;
}

/**
 * Registers the handler for a DELETE Request.
 *
 * @param \Closure $fn
 * @return void
 */
function delete($fn)
{
    $trace = debug_backtrace();
    $controller = $trace[0]["file"];

    Razor::controller($controller)->delete = $fn;
}

/**
 * Registers the handler for a PATCH Request.
 *
 * @param \Closure $fn
 * @return void
 */
function patch($fn)
{
    $trace = debug_backtrace();
    $controller = $trace[0]["file"];

    Razor::controller($controller)->patch = $fn;
}

/**
 * Registers the handler for a PUT Request.
 *
 * @param \Closure $fn
 * @return void
 */
function put($fn)
{
    $trace = debug_backtrace();
    $controller = $trace[0]["file"];

    Razor::controller($controller)->put = $fn;
}

/**
* Registers the handler for a HEAD Request.
 *
 * @param \Closure $fn
* @return void
*/
function head($fn)
{
    $trace = debug_backtrace();
    $controller = $trace[0]["file"];

    Razor::controller($controller)->head = $fn;
}

/**
 * Registers the handler for an OPTIONS Request.
 *
 * @param \Closure $fn
 * @return void
 */
function options($fn)
{
    $trace = debug_backtrace();
    $controller = $trace[0]["file"];

    Razor::controller($controller)->options = $fn;
}

/**
 * Registers the handler to be called when an error is encountered.
 *
 * @param \Closure $fn
 * @return void
 */
function error($fn)
{
    $trace = debug_backtrace();
    $controller = $trace[0]["file"];

    Razor::controller($controller)->error = $fn;
}

/**
 * Registers the handler to be called when no other handler can be invoked.
 *
 * @param \Closure $fn
 * @return void
 */
function notFound($fn)
{
    $trace = debug_backtrace();
    $controller = $trace[0]["file"];

    Razor::controller($controller)->notFound = $fn;
}

/**
 * Handles the Request.
 *
 * @param \Closure $fn
 * @return void
 */
function run()
{
    $trace = debug_backtrace();
    $controller = $trace[0]["file"];

    Razor::run($controller);
}

/**
 * Registers services with the framework.
 *
 * @param array $factories
 */
function services(array $factories)
{
    foreach ($factories as $name => $factory) {
        Razor::service($name, $factory);
    }
}
