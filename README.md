# Razor

Razor is a lightweight micro framework based around the concept of REST. It
is similar to a number of projects like [Slim](http://www.slimframework.com)
and [Silex](http://silex.sensiolabs.org/) but is designed to be syntactically
nicer to use, from personal preference.

As noted above the framework is focused around RESTful web applications. Lets
say we are working on the obligatory TODO application- we are going to have
an *end point* in our API that represents the collection of TODO items or,
more correctly, *resources*.

To model this in Razor, we would create a file in our web-facing folder called
`todos.php` which would contain the following code:

```php
<?php

// File: todos.php

require_once "/path/to/vendor/autoload.php";

use Razor\EndPoint;

(new EndPoint())

    ->run();

```

We can think of this file as our *resource collection*; i.e. if we want to add,
edit, get, or remove items TODO items then that change comes through this file.
Razor is geared towards this mindset and so all the logic pertaining to a resource
is encapsulated in the `EndPoint` object. To be able to perform actions against
our resource we have to *assign a delegate into a slot representing the HTTP
request method*, or more simply:

```php
<?php

// File: todos.php

require_once "/path/to/vendor/autoload.php";

use Razor\EndPoint;

(new EndPoint())

    ->get(function ()
    {
        // Hey, that was easy!
    })

    ->run();

```

Anyone used to the format of other micro-frameworks should recognise this
syntax. Essentially, in the instance that the web browser makes an HTTP
GET request, the code in the delegate (or Closure) is invoked. There are
method calls for each of the major HTTP Verbs, and more may be supported
in the future.

Great! So our client can access our `todo.php` page from a browser and they
see... Nothing. Lets fix that by sending the browser a message:

```php
<?php

// File: todos.php

require_once "/path/to/vendor/autoload.php";

use Razor\EndPoint;
use Razor\Services\Http;

(new EndPoint())

    ->get(function (Http $http)
    {
        return $http->response->standard("Hello, world!");
    })

    ->run();

```

Now when the client connects they will get the message `Hello, world!`. You
may have noticed that you didn't have to specify that the `$http` object
exists anywhere in this file and that it has been passed through as an argument
to the delegate. What is happening here is called Service Injection.

A service is an object or value that provides some functionality to your
application. When Razor decides to invoke the delegate for the HTTP GET request,
it examines the arguments that are specified and looks to see if it can
provide them. If it can, it then *injects* those arguments when the delegate
is invoked. If you have an AngularJS background this will seem native to you,
if not then this may feel a little foreign. The goal is to make your delegate
stateless, i.e. not this:

```php
<?php

// File: todos.php

require_once "/path/to/vendor/autoload.php";

use Razor\EndPoint;
use Razor\Services\Http;

$http = new Http(/* Have to supply all the arguments here ... */);

(new EndPoint())

    ->get(function () use ($http)
    {
        return $http->response->standard("Hello, world!");
    })

    ->run();

```

This is because, firstly, you always have to create the object or write
quite ugly code to lazy load it, secondly because `function () use (...)` is
really terse, ugly and becomes difficult to maintain over time and, thirdly,
because it makes testing this code nearly impossible, or at the very least
impossible to test in isolation. As such we can let the framework handle the
ugly bits and not let it get in the way of our design.

In this particular instance, `$http` is a service that Razor ships with, but
you can use services for your own code too. Say we have an object already that
handles the management of a todo's database, we can specify it in our code as
follows:

```php
<?php

// File: todos.php

require_once "/path/to/vendor/autoload.php";

use Razor\Razor;
use Razor\EndPoint;
use Razor\Services\Http;

Razor::environment()
     ->services()
     ->register("todoRepo", function ()
     {
        return new TodoRepo(/* ... */);
     });

(new EndPoint())

    ->get(function (Http $http, TodoRepo $todoRepo)
    {
        $id = $http->request->get("id");

        $data = $todoRepo->get($id);

        return $http->response->json($data);
    })

    ->run();

```

Here we register the service with the frameworks `Environment` using a delegate.
Then in our code, we specify this service as a dependency. **It is important to
note that it is the name `todoRepo` that is used to find the service and not the
type hint**. This is because we may end up with multiple objects with the same
type needing to be injected, like different log objects that both use the same
API.

Also important to note is that this delegate can also receive injected services,
like:

```php

Razor::environment()
     ->services()
     ->registerMany(array(
         "conn", function ()
         {
            return new PDO(/* ... */);
         },

         "todoRepo", function (PDO $conn)
         {
            return new TodoRepo($conn);
         }
     ));

```

*N.B. It is suggested that you move these service registrations to a
`bootstrap.php` file so that as your application grows you can keep
on top of it.*

Lets say you want to open this app out onto the web, but you want to
make sure you have some security in place. You could expose a service to
handle this, but that will undoubtedly lead to lots of boilerplate. As
such the framework ships with the well known idea of *Middleware*.

A Middleware is a small object which is designed to be called before your
delegate, and perform request filtering or response amendments. This means
you can write small pieces of functionality and compose your handling from
this. An example of a middleware is:

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

We can then utilise the `SecurityMiddleware` by:

```php
<?php

// File: todos.php

require_once "/path/to/vendor/autoload.php";

use Razor\EndPoint;
use Razor\Services\Http;

(new EndPoint())

    ->get(new SecurityMiddleware(function (Http $http, TodoRepo $todoRepo)
    {
        $id = $http->request->get("id");

        $data = $todoRepo->get($id);

        return $http->response->json($data);
    }))

    ->run();

```

As such the `SecurityMiddleware` will be called first and if the connection
is secure it will then invoke our application logic for the `GET` request.
We can also chain middleware together:

```php
<?php

// File: todos.php

require_once "/path/to/vendor/autoload.php";

use Razor\EndPoint;
use Razor\Services\Http;

(new EndPoint())

    ->get(new SecurityMiddleware(new EnsureJsonMiddleware(function (Http $http, TodoRepo $todoRepo)
    {
        $id = $http->request->get("id");

        $data = $todoRepo->get($id);

        return $http->response->json($data);
    })))

    ->run();

```


## About Razor

The framework is designed to be developer friendly. You want to write well
structured code, but you also need to provide justification for every line
of code in your project? Razor is as lightweight as possible, using a simple
API for [Service Injection](https://github.com/rawebone/Injector) and the
well trusted Symfony HTTP Foundation library in it's HTTP Service.

You want to write code that is easily maintainable? Services allow you to
keep your shared logic accessible without the need for excessive boilerplate
in your application logic. Keep your application logic in the URL end points
so you can easily identify what is going on.

Overall, Razor is designed to give you an elegant way of working with HTTP
requests without bloat. If it fits your work flow, then more power to you!


## Installation

Installation is via [Composer](https://getcomposer.org), add Razor to your
dependencies:

```json
{
    "require": {
        "rawebone/razor-library": "dev-master"
    }
}
```

Once the project goes to stable, there will be two branches available for
installation:

* 1.x.y
* 1.x.y-compat

The `compat` branch will allow users of PHP5.3 and above to use the framework
while the main branch will be PHP5.4 and above.


## License

[MIT License](LICENSE), go wild.
