# Razor Framework

Razor is a minimalist web application framework which provides a bridge
between "classic" and "modern" PHP. Its goal is to make developing
applications simple and to enable testable and easy maintenance. The system
is opinionated about your development workflow and provides a structure
for working in.


## Quick Start

To get going with the framework quickly, open the `public/index.php` file. In
Razor this is referred to as a **Controller** and contains handlers for taking
action against particular **HTTP Verbs**. For example when we request the index
page of the website, to handle a GET request and send out the obligatory "Hello,
World!" response we would write:

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

Say our requirements change and we need to output the name of the user who has
made the request. The framework provides a mechanism called **Service Injection**
which allows us to pass through objects identified by a name; this allows us to
_de-couple the handlers from their dependencies and keeps the Controllers clean
and concise_. The framework provides a default service which can be used for
accessing request data safely:

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
`Hello, Bob!` and if we just hit `index.php` we get `Hello, World!`. In
addition to the request service we also have a response service which makes
it easier to send data back to the client:

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

This response object allows us to work with headers in a more general way than
is possible with native PHP; in addition we can use it to handle particular
types of data responses, for example JSON:

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

All of the headers and encoding process can be deferred and best practices
(such as ensuring all data going into the output JSON is UTF-8 encoded first)
can be applied without us having to do the work manually each time.


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

