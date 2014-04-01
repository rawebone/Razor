<?php

///
/// Razor Example application file
/// ==============================
///

require_once __DIR__ . "/../framework/razor.php";

//
// The framework is designed around a simple idea:
// your pages are your controllers. This allows
// you to see quite quickly what is happening when
// the page request hits. It's a classic idea, but
// when you are writing a new application and just
// need to get it done, this approach is not a bad
// one.
//

use Symfony\Component\HttpFoundation\Request;
use Psr\Log\LoggerInterface;

// If the page has been hit with a GET method, this handler will be called.
// The objects $_request and $_log will be automatically injected by the
// framework - you can even inject your own services through the same
// mechanism. This allows us to reduce the amount of code we have to
// write in our controller and enables us to easily test the handlers later on.
get(function (Request $_request, LoggerInterface $_log)
{
    $_log->debug("I've been called!");
});

// If an error occurs during execution of the get() handler, this
// error handler will be invoked.
error(function ()
{

});

// Run the application
run();
