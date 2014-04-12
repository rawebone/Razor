# Razor Framework

Razor is a minimalist web application framework which provides a bridge
between "classic" and "modern" PHP. Its goal is to make developing
applications simple and to enable testing and easy maintenance. The system
is opinionated about your development workflow and provides a structure
for working in.


## Concepts

To get going with the framework quickly, open the `public/index.php` file. In
Razor this is referred to as a **Controller** and it contains **Handlers** for
taking action against particular **HTTP Verbs**. For example when we request
the index page of the website, to handle a `GET` request and send out the
obligatory "Hello, World!" response we would write:

```php
<?php

// File: public/index.php

require_once(__DIR__ . "/../framework/razor.php");

get(function ()
{
    echo "Hello, World!";
});

run();

```

Any other HTTP requests (like a `POST` request) will respond as a `400 Bad Request`
because we do not have a handler for them and as such these requests are invalid.
The `get()` and `run()` functions are provided by the framework to make it easy
to work with these HTTP Verbs and all handlers in the controller are defined using
**Closures**, one of the nicer features of PHP5.3+.

Say our requirements change and we need to output the name of the user who has
made the request. Razor provides a small number of API's called **Services**,
one of which allows us to safely access information about the request. We can
use this service by declaring it as a parameter of our handler:


```php
<?php

// File: public/index.php

require_once(__DIR__ . "/../framework/razor.php");

get(function ($request)
{
    printf("Hello, %s!", $request->get("name", "World"));
});

run();

```

So now, if we hit the Index Controller as `index.php?name=Bob` we can see
`Hello, Bob!` and if we just hit `index.php` we get `Hello, World!`. The
services system is made possible by a technique called **Dependency Injection**
which allows us to pass through objects based upon a name. When Razor finds
a handler which matches the current HTTP verb, it quickly reads the handlers
signature to determine what services it needs and _injects_ them when it
is invoked. In this case the framework sees that the `request` service is
required and passes it through.

Working in this way allows us to _de-couple the handlers from their dependencies_
and keeps the controllers clean and concise. We define and consume the services
for our application in exactly the same way as will be described later on.

In addition to the `request` service we also have a `response` service for
sending information back to the client:

```php
<?php

// File: public/index.php

require_once(__DIR__ . "/../framework/razor.php");

get(function ($request, $response)
{
    $name = sprintf("Hello, %s!", $request->get("name", "World"));
    $response->general($name)->send();
});

run();

```

This `response` object is an abstraction over the native PHP header and
output handling which makes it easier to send HTTP Responses back to the
client. Here we are sending a `200 Okay` response to the client with the
content of `Hello, World!`. This response object is particularly helpful
when dealing with more complex output, like JSON, where we need to send
particular headers so that clients can read the data effectively.

For example, JSON can be returned by:

```php
<?php

// File: public/json-today.php

require_once(__DIR__ . "/../framework/razor.php");

get(function ($request, $response)
{
    $data = array(
        "year"  => date("Y"),
        "month" => date("m"),
        "day"   => date("d")
    );

    $response->json($data)->send();
});

run();

```

All of the headers, encoding process and best practices (such as ensuring all
data going into the output JSON is UTF-8 encoded first) can be applied without
us having to do the work manually each time, which makes our application more
secure.


## Folder Structure

As outlined at the top of this document, Razor is an opinionated framework.
That means that it provides a folder structure to work with which needs to
be conformed to. Later versions of the framework will implement something
that will require this opinion, and this will be awesome, but for now we
need to build our applications using this structure.

```

/application                <-- Where you're application is configured
    /resources              <-- Template files, site assets etc
    /src                    <-- Custom classes/libraries your application requires
    /tests                  <-- The tests for your application

/framework                  <-- The Razor Framework

/public                     <-- Controllers, anything that needs to be publicly accessible
    /assets                 <-- CSS Files, JavaScript Files, static images etc

```


## The Application Bootstrap

As you develop your application you'll find that you want to use libraries,
define classes and make sure you're application can run properly. For this
we have the `application/bootstrap.php` file which is called automatically
when we load the framework.


## Defining Services

Services are at the core of the framework as they allow us to lazily load
components of the application and pass them around with ease. For example,
if you want to share an object which contains credentials for a database
as a service, we would put the following into our `application/bootstrap.php`
file:

```php
<?php

// File: application/bootstrap.php

services(array(
    "credentials" => function ()
    {
        return (object)array("user" => "mike", "pass" => "beta");
    }
));

```

In our controllers, we can now access this service in the same way as shown in
the Quick Start:

```php
<?php

// File: public/db-controller.php

require_once(__DIR__ . "/../framework/razor.php");

get(function ($credentials)
{

});

run();

```

More importantly, services can consume _other_ services:

```php
<?php

// File: application/bootstrap.php

services(array(
    "credentials" => function ()
    {
        return (object)array("user" => "mike", "pass" => "beta");
    },

    "connection" => function ($credentials)
    {
        return new DbConnection($credentials->user, $credentials->pass);
    }
));

```

This is an incredibly powerful feature of the framework which allows you to
expose and compose services in your application with ease.
