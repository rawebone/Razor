<?php

///
/// Razor Example application file
/// ==============================
///

require_once __DIR__ . "/../framework/razor.php";

use Symfony\Component\HttpFoundation\Request;
use Psr\Log\LoggerInterface;

// If the page has been hit with a GET method, this handler will be called
// The objects $_request and $_log will be automatically injected by the
// framework
get(function (Request $_request, LoggerInterface $_log)
{

});

// If an error occurs during execution of the get() handler, this
// error handler will be invoked.
error(function ()
{

});

// Run the application
run();
