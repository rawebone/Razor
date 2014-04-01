<?php

function get($fn)
{
    injector("_http")->registerVerb(__FUNCTION__, $fn);
}

function post($fn)
{
    injector("_http")->registerVerb(__FUNCTION__, $fn);
}

function put($fn)
{
    injector("_http")->registerVerb(__FUNCTION__, $fn);
}

function delete($fn)
{
    injector("_http")->registerVerb(__FUNCTION__, $fn);
}

function patch($fn)
{
    injector("_http")->registerVerb(__FUNCTION__, $fn);
}

function error($fn)
{
    injector("_http")->registerErrorHandler($fn);
}

function notFound($fn)
{
    injector("_http")->registerNotFoundHandler($fn);
}

function run()
{
    injector("_http")->run();
}
