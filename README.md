# Razor

Razor is a simple micro framework based somewhat on [Slim](http://www.slimframework.com)
and [Silex](http://silex.sensiolabs.org/) but designed to provide more straight-forward
syntax and testing. This documentation is still in progress, but the API is pretty-well
commented so should provide enough to delve right into.


## Basic Usage

Like the other micro frameworks sighted above, Razor follows the traditions of Middleware
and HTTP Verb mapping to achieve simple solutions to web problems. Unlike other solutions,
Razor **does not** use any kind of routing. This is because I am primarily using it for
my own RESTful needs and as such there is really no difference to me between `/api/endpoint.php`
and `/api/endpoint/` to justify building in a more complex system. That being said it should
be permissive enough for using with a router by simply using includes with a system like
Klien et al.

Because there is no router, the page itself becomes the "controller" and can have various
different operations handled inside of it. Controller is in the old inverted commas as
really, speaking again from REST, they are more correctly known in Razor parlance as
End Points. As such, the most basic of applications in the framework is:

```php
<?php

// File: public/index.php

require_once(__DIR__ . "/../vendor/autoload.php");

use Razor\EndPoint;
use Razor\Razor;

$ep = (new EndPoint())

    ->get(function ()
    {
        echo "Hello, World!";
    });

Razor::run($ep);

```

In this example, when the index page of our application is requested, an `EndPoint`
object will be configured to echo "Hello, World!" when the request is made with the
`GET` HTTP verb. This configured `EndPoint` is then run against the request.

The `EndPoint` object accounts for most of the basic HTTP Verbs, but should you need more
you can simply create an extension from the EndPoint object to suit your own needs. There
is not uber fancy handling at work in the background.

Our `EndPoint` can also specify handlers to be invoked in two special circumstances:

```php

$ep = (new EndPoint())

    // This will be invoked when an error occurs in processing of the current method
    ->onError(function ()
    {

    })

    // This will be called when the HTTP Method is not backed by a delegate
    ->onNotFound(function ()
    {

    });

```

These are defaulted to provide appropriate server responses and can be overridden as
shown above on a per-end point basis. If you need to add in specific, global handling
you should extend the `EndPoint` object, and overload the `__construct()` method.


## Beyond The Basics

## The Environment

Razor provides a system for configuring runtime behaviour via the `Environment` object:

```php
<?php

// File: bootstrap.php

use Razor\Environment;
use Razor\Razor;

$environment = new Environment();

// This prevents the framework from catching exceptions
// raised during execution of an HTTP Verb delegate.

// True by default
$environment->development = false;

// This option prevents the framework from dispatching
// full-stop, see later on with regards to testing.

// False by default
$environment->testing = false;


// You register your environment through the Razor class:
Razor::environment($environment);

```


### Services

Razor ships with a Service Injection system. If you've seen container systems
like Pimple before, think that but with those service names injected for you:

```php
<?php

// File: bootstrap.php

use Razor\Environment;
use Razor\Razor;


$environment = new Environment();
$services = $environment->services();

$services->register("service", function ()
{
    return new MyService();
});

$services->registerMany([
    "aardvark" => new sdtClass(),

    "silly" => function () { return new SillyService(); }
]);

Razor::environment($environment);

```

```php
<?php

// File: index.php


// ...

$ep = (new EndPoint())

    ->get(function (MyService $service)
    {
        $service->callSomething();
    });

// ...

```

This allows us to reduce code bloat in our application quite nicely and keep
everything readable. Services have to be registered with the Environment in
order for the application to work effectively. The important thing here is
the name of the parameter -- "service" in this case -- as it allows us to
find the object we need in the container. Types are checked for safety.

A basic service call `http` is included which provides a wrapper over the
Symfony HttpFoundation. You can see this API in the `Razor\Services\Http`
object.


### Middleware

Razor ships with a middleware solution, built on-top of the Service
injection system. The goal of middleware is to provide small, generic
handling to problems and can be conceived of as being like the layers
of an onion - you invoke a middleware, and it then performs an action,
calling the next middleware in the chain.


```php

// File: src/SecurityMiddleware.php

use Razor\Middleware;
use Razor\Services\Http;

class SecurityMiddleware extends Middleware
{
    public function __invoke(Http $http)
    {
        if (!$http->request->isSecure()) {
            return $http->response->standard("Whoa! Your connection is not secure!", 400);
        }

        return $this->invokeDelegate();
    }
}

```

This example allows us to do basic filtering and handling on a per HTTP verb delegate
basis:


```php

// File: public/index.php

// ...

$ep = (new EndPoint())

    ->get(function ()
    {
        // Secure, insecure - who cares?
    })

    ->post(new SecurityMiddleware(function()
    {
        // This is a secure connection
    });

// ...

```

Middleware can also be used to wrap the target, say for exception handling
or the like. We could also use this for firewalling or logging access. The
sky is the limit. Middleware can also wrap Middleware:

```php

// File: public/index.php

// ...

$ep = (new EndPoint())

    ->get(new Middleware1(new Middleware2(function ()
    {

    })));

```


### Responses

The framework is designed to take some of the boilerplate out of your
coding, and part of this is in the way it handles responses. This also
impacts upon testability of your application and, unless absolutely
necessary, is the way you should write your code using the framework:

```php
<?php

// File: public/my_response.php

// ...

$ec = (new EndPoint())

    ->get(function (Http $http)
    {
        return $http->response->standard("Hello, World!");

        // as opposed to:

        $http->response->standard("Hello, World!")->send();
    });


// ...

```

This allows you to test that the generated response is valid.

### Testing

TODO

## License

[MIT License](LICENSE), go wild.

