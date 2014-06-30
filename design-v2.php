<?php

// One of the bigger problems with Razor in its current state is that
// it has tried to provide too much, and this has caused pain in development.
// The biggest of these pains is in the idea that it could hold multiple controllers
// and that it could avoid global state.


// As such, we now need a new way of handling which maintains the best possible
// syntax and dispatch, without the pains.


// Firstly, we're moving away from the global functions as this
// was just too damned horrible. Moving this to a singleton may
// not ultimately prove much better but syntactically r::get() > (r\get() | $r->get())
// because it looks better but also because it does not require holding the
// object in the domain of the end user.
use Razor\Razor as r;


// This means that calling run will have no effect - this option should be
// used in Unit Tests to enable E2E testing
r::option("testing", true);

// This option means that error output will be displayed/exceptions thrown
r::option("development", true);

// This method denotes that the controller will be reset to the default.
// This allows us to test multiple controllers without having to build
// more complicated architectures for supporting controllers by name.
r::reset();

// Throws an HttpAbortException - much easier than the manual way
r::abort();

// In v1 we exposed two services - $request and $response
// but most of the time these services are required together
// and as such they should be merged into a single, HTTP API
use Razor\Http;

r::get(function (Http $http)
{
	$http->request->get("blah");

	// As another point - to ensure testability the delegate
	// should always return a response and not simply send this
	// out. That enables the framework greater control of flow
	// for Unit Testing purposes. The framework will not quibble
	// on this point as it is up to the end users discretion, but
	// in general it should be a returned response.
	return $http->response->standard("Hello");
});

// To enable better testing, we can use the run method.
// This will support a return value
r::run($request, $response);

// Rather than having the error() and notFound() functions
// as in V1, we will use a more generic event dispatch system
// to keep the DSL clean. It also means that the events are not
// tied to the controller, meaning we can more easily make generic
// error handling.
r::event("http.not-found", function ()
{

});

r::fire("http.not-found");

// The event API also supports the injection system. In addition,
// pre- and post-run events will be added to enable more concise
// handling.

// Logging is also improved - in V1 there was an $applog service
// which had to be backed by configuration and a Filesystem object.
// This is placing too much responsibility on the library for logging
// and as such we will support the ability to log, using the NullLogger
// by default:
r::setLogger(new Logger());

// Users can then expose their particular instance to the framework
// during service configuration:
r::services(array(
	"logger" => function ()
	{
		$log = new Logger();

		// Only set this if we are currently using Razor -
		// this allows for users to use the same service configuration
		// between the Web and CLI Scripts
		if (class_exists('Razor\Razor', false)) {
			Razor\Razor::setLogger($log);
		}

		return $log;
	}
));


// Middleware.
// The goal of Middleware is to simplify the task of maintaining the code base
// by delegating responsibilities to small utility classes. These could handle
// authentication or response data encoding. The big problem with a middleware
// stack is that you have to then pass state between the layers which means
// we have to encapsulate the package to be passed. In addition is it not
// easy to immediately rationalise the function of the middleware.

// As such, using an MiddlewareAbstract will enable us to construct a chain
// of middleware that can easily be reasoned about and extended. It will
// inevitably lead to duplication of filters, but this also makes it easier
// to apply filters selectively.
r::post(new Authoriser(new ResponseEncoder(function ()
{

	//

})));


// The goal of these changes is to provide the end users with a more complete and concrete
// API for achieving their goals.


