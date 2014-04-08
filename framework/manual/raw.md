# Razor Framework

Razor is a minimalist web application framework for PHP which is designed to
to unify some of the good ideas of "classic" PHP development with some of the
good ideas of "modern" development.

The goal of the framework is to provide some basic scaffolding for development
and providing an easy mechanism for testing the application - as such the effects
of maintenance should be lessened and the stability of the application increased.

## An Example

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

However the goal of the framework is on consistent and sustainable design. For example,
say we need to improve our example to take on a Name parameter:

```php
<?php

// ...

get(function ($request)
{
    printf("Hello, %s", $request->get("name", "World"));
});


// ...


```
