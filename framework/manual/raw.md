# Razor Framework

Razor is a minimalist web application framework for PHP which is designed to
to unify some of the good ideas of "classic" PHP development with some of the
good ideas of "modern" development.

The goal of the framework is to provide some basic scaffolding for development
and providing an easy mechanism for testing the application - as such the effects
of maintenance should be lessened and the stability of the application increased.

## Usage

**Razor uses `Controllers` to handle HTTP Requests.** That's it's core job, and
 to do this it provides you with a natural language for interacting with the
 various _HTTP Verbs_ that you need:

```php
<?php

// public/index.php

// Load the framework
require_once(__DIR__ . "/../framework/razor.php");

get(function ()
{
    echo "Hello, World!";
});

// Dispatch the request
run();

```

In this example, the _Controller_ `index.php` specifies that when an HTTP GET Request
is received it should output "Hello, World!". Any other requests that are made upon
the Controller will return a 404 Response. You could write this out as:

```php
<?php

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    echo "Hello, World!";
} else {
    header("HTTP/1.1 404 Not Found", 404);
}

```

For the most trivial of applications the above is fine, but if you need to handle more
complex requests, like those with parameters or that handle multiple verbs it becomes
less clear what is being attempted:

```php
<?php

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    // ...
} else if ($_SERVER["REQUEST_METHOD"] === POST) {
    // ...
} else {
    // ...
}


```

In addition, applications need to create and manage objects to help them perform
their job:

```php
<?php

// public/index.php

$api = new My_Api_Object(new DbConnection());
$log = new Logger();

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    // ...
}

```

Quickly this script starts to become more and more complex and to test it becomes
impossible without a full manual test; in addition while manual testing is
positive humans are prone to error and without code level testing small errors
creep in and lead to bugs later on.

### A Better Way

Razor allows us to segregate our code by what HTTP Verb we are expecting using
handlers as we've seen above. These handlers can have **Services injected**
into them so that they can access the business logic easily. With Razor, our
above example can be re-written as:

```php
<?php

// public/index.php

require_once(__DIR__ . "/../framework/razor.php");

services(array(
    "api" => function () { return new My_Api_Object(new DbConnection()); },
    "log" => function () { return new Logger(); }
));

get(function ($api)
{
    // ...
});

post(function ($api, $log)
{
    // ...
});

run();

```

As they are required, the services are created and passed through into your
handler. The benefit here is that we are requesting the `api` service be
passed through, which means that we can easily pass through test doubles
to our handlers. This process is called **de-coupling** because we have
isolated the handler from it's dependencies (`api` and `log`) and from
it's immediate execution.


